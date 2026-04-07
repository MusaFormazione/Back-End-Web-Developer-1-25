<?php

# Scrivere una funzione che ritorni il copyright con anno corrente:
# Tipo: Copyright AAAA c MYCOMPANY s.r.l.

function printCopyRight(): string{
	$dateArray = getDate(null);
	$year = $dateArray['year'];
	return "Copyright $year c MYCOMPANY s.r.l.";
}

echo printCopyRight();