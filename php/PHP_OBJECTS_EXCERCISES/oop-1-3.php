<?php

class StudentGradesContainer {

	private array $students;

	function addStudent( string $name, array $grades ):void {
		$this->students[] = [
			'name'   => $name,
			'grades' => $grades
		];
	}

	public function printReport():void {
		foreach ( $this->students as $student ) {
			$average = $this->getAverage( $student['grades'] );
			echo "Studente: {$student['name']}, Media voti: $average\n";
		}
	}

	private function getAverage( array $grades ):float {
		return round(array_sum( $grades ) / count( $grades ), 2);
	}
}

$studentContainer = new StudentGradesContainer();

$studentContainer->addStudent( "Luca", [ 7, 8, 9 ] );
$studentContainer->addStudent( "Anna", [ 6, 7, 6 ] );
$studentContainer->printReport();