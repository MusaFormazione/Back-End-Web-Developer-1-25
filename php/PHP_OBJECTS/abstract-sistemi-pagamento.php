<?php

interface MetodoPagamentoContract {
	function execute(float $importo):void;
	function check():bool;
}

abstract class MetodoPagamento implements MetodoPagamentoContract {
	private bool $paymentExecuted = false;

	abstract protected function internalExecute(float $importo);

	final function execute(float $importo): void {
		try{
			$this->paymentExecuted = false;
			$this->internalExecute($importo);
			$this->paymentExecuted = true;
		} catch (Exception $e){
			$this->paymentExecuted = false;
		}
	}

	function check(): bool {
		return $this->paymentExecuted;
	}
}

class Bonifico extends MetodoPagamento{
	protected function internalExecute(float $importo) {
		echo "Bonifico elaborato: €$importo\n";
	}
}

class CartaCredito extends MetodoPagamento{
	private float $expenses;
	private string $name;
	private float $limit;

	function __construct( string $name, float $limit ) {
		$this->name = $name;
		$this->limit = $limit;
		$this->expenses = 0;
	}

	protected function internalExecute(float $importo) {
		$expenses = $this->expenses;
		$expenses += $importo;
		if($expenses > $this->limit){
			throw new Exception("Over limit");
		}
		$this->expenses = $expenses;
	}

	// Metodo pubblico custom
	public function getExpenses() {
		return $this->expenses;
	}

	// Altro metodo pubblico
	public function resetExpenses() {
		$this->expenses = 0;
		echo "Spese resettate\n";
	}
}

class Paypal extends MetodoPagamento{
	private string $email;
	private float $balance;

	function __construct( string $email ) {
		$this->email = $email;
		$this->balance = 0;
	}

	protected function internalExecute(float $importo) {
		$this->balance -= $importo;
	}

	public function addBalance(float $value){
		$this->balance += $value;
		echo "Saldo aggiunto: €$value. Totale: €{$this->balance}\n";
	}

	public function getBalance() {
		return $this->balance;
	}
}

enum PaymentMethod {
	case CARTA;
	case PAYPAL;
	case BONIFICO;
}

function GetPaymentMethod(PaymentMethod $methodName): MetodoPagamentoContract {
	if($methodName == PaymentMethod::PAYPAL)
		return new Paypal("email@test.it");

	if($methodName == PaymentMethod::CARTA)
		return new CartaCredito("Paolo Rossi", 1000);

	if($methodName == PaymentMethod::BONIFICO)
		return new Bonifico();

	throw new Exception("out of range");
}

// ========== FUNZIONE PER TROVARE PRIMO METODO PUBBLICO ==========

function getPrimoMetodoPublico($oggetto) {
	$reflection = new ReflectionClass($oggetto);
	$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

	// Filtra i metodi dell'interfaccia
	$metodi_personalizzati = array_filter($methods, function($method) {
		return !in_array($method->getName(), ['execute', 'check']);
	});

	// Ritorna il primo metodo personalizzato
	if (count($metodi_personalizzati) > 0) {
		return reset($metodi_personalizzati);
	}

	return null;
}

// ========== FUNZIONE PER INVOCARE DINAMICAMENTE ==========

function invocarePrimoMetodoPublico($oggetto, ...$params) {
	$metodo = getPrimoMetodoPublico($oggetto);

	if ($metodo === null) {
		echo "❌ Nessun metodo pubblico trovato!\n";
		return null;
	}

	$nomeMetodo = $metodo->getName();
	echo "✓ Invocando metodo: " . get_class($oggetto) . "->$nomeMetodo()\n";

	// Richiama il metodo
	return $metodo->invoke($oggetto, ...$params);
}

// ========== UTILIZZO ==========

echo "=== CARTA DI CREDITO ===\n";
$carta = GetPaymentMethod(PaymentMethod::CARTA);
echo "Classe: " . get_class($carta) . "\n";

// Primo metodo pubblico è getExpenses()
invocarePrimoMetodoPublico($carta);
echo "\n";

echo "=== PAYPAL ===\n";
$paypal = GetPaymentMethod(PaymentMethod::PAYPAL);
echo "Classe: " . get_class($paypal) . "\n";

// Primo metodo pubblico è addBalance()
invocarePrimoMetodoPublico($paypal, 500);
echo "\n";

echo "=== BONIFICO ===\n";
$bonifico = GetPaymentMethod(PaymentMethod::BONIFICO);
echo "Classe: " . get_class($bonifico) . "\n";

// Nessun metodo pubblico personalizzato
invocarePrimoMetodoPublico($bonifico);
echo "\n";

// ========== VERSIONE AVANZATA: INVOCARE CON PARAMETRI ==========

function invocarePrimoMetodoPublicoAvanzato($oggetto, ...$params) {
	$reflection = new ReflectionClass($oggetto);

	// Ottieni tutti i metodi pubblici
	$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

	// Filtra i metodi dell'interfaccia
	$metodi_validi = array_filter($methods, function($method) {
		return !in_array($method->getName(), ['execute', 'check']);
	});

	if (count($metodi_validi) === 0) {
		throw new Exception("Nessun metodo pubblico trovato in " . get_class($oggetto));
	}

	$metodo = reset($metodi_validi);
	$nomeMetodo = $metodo->getName();
	$nomeClasse = get_class($oggetto);

	// Verifica parametri
	$parametriRichiesti = $metodo->getNumberOfRequiredParameters();
	$parametriPassati = count($params);

	echo "📋 Classe: $nomeClasse\n";
	echo "🔧 Metodo: $nomeMetodo()\n";
	echo "📊 Parametri richiesti: $parametriRichiesti\n";
	echo "📊 Parametri passati: $parametriPassati\n";

	if ($parametriPassati < $parametriRichiesti) {
		throw new Exception("Parametri insufficienti per $nomeMetodo()");
	}

	echo "✓ Invocazione...\n";
	return $metodo->invoke($oggetto, ...$params);
}

echo "=== VERSIONE AVANZATA ===\n";
echo "\n--- PayPal con parametro ---\n";
try {
	invocarePrimoMetodoPublicoAvanzato($paypal, 1000);
} catch (Exception $e) {
	echo "❌ Errore: " . $e->getMessage() . "\n";
}

echo "\n--- Carta senza parametri ---\n";
try {
	invocarePrimoMetodoPublicoAvanzato($carta);
} catch (Exception $e) {
	echo "❌ Errore: " . $e->getMessage() . "\n";
}

// ========== LISTA TUTTI I METODI PUBBLICI ==========

function elencaTuttiMetodiPublici($oggetto) {
	$reflection = new ReflectionClass($oggetto);
	$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

	echo "\n📚 Metodi pubblici di " . get_class($oggetto) . ":\n";

	foreach ($methods as $method) {
		$parametri = $method->getParameters();
		$nomeParametri = implode(", ", array_map(function($p) {
			return $p->getName();
		}, $parametri));

		echo "  - {$method->getName()}($nomeParametri)\n";
	}
}

elencaTuttiMetodiPublici($carta);
elencaTuttiMetodiPublici($paypal);
elencaTuttiMetodiPublici($bonifico);