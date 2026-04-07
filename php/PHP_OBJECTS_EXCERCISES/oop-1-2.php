<?php


class Cart {

	private array $cart;

	function addProduct( string $productName, float $price, int $quantity ): void {
		$this->cart[] = [
			'name'     => $productName,
			'price'    => $price,
			'quantity' => $quantity
		];
	}

	function removeProduct(int $index):void {
		unset($this->cart[$index]);
	}

	function getTotal():float {

		$total = 0;

		foreach ( $this->cart as $item ) {
			$total += $item['price'] * $item['quantity'];
		}

		return $total;
	}

}

$cart = new Cart();
$cart->addProduct("Laptop", 800, 1 );
$cart->addProduct("Mouse", 20, 2 );
$cart->removeProduct(1);

echo "Totale carrello: " . $cart->getTotal() . "€\n";
