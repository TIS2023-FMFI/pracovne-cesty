<?php

return [
	// bin path to your pdflatex installation | use 'which pdflatex' on a linux system to find out which is the path to your pdflatex installation
	'binPath' => '/usr/bin/pdflatex',

	// bin path to your bibtex installation | use 'which bibtex' on a linux system to find out which is the path to your bibtex installation
	'bibTexPath' => '/usr/bin/bibtex',

	// Folder in your storage folder where you would like to store the temp files created by LaraTeX
	'tempPath' => 'app/pdf_exports',

	// boolean to define if log, aux and tex files should be deleted after generating PDF
	'teardown' => true,
];
