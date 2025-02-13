\documentclass[10pt,a4paper]{article}

\usepackage[utf8]{inputenc}
\usepackage[T1]{fontenc}
\usepackage{geometry}
\usepackage{array}
\usepackage{tabularx}
\usepackage{setspace}
\usepackage{xcolor}
\usepackage{amssymb}

\geometry{a4paper,left=20mm,right=20mm,top=20mm,bottom=20mm}
\setlength\parindent{0pt}

\newcommand{\PreserveBackslash}[1]{\let\temp=\\#1\let\\=\temp}
\newcolumntype{C}[1]{>{\PreserveBackslash\centering}p{#1}}
\newcolumntype{R}[1]{>{\PreserveBackslash\raggedleft}p{#1}}
\newcolumntype{L}[1]{>{\PreserveBackslash\raggedright}p{#1}}

\renewcommand{\familydefault}{cmr}
\pagenumbering{gobble}

\newcommand{\placeholder}[1]{< #1 >}

\newcommand{\sofiaID}{@latex($sofiaID)}
\newcommand{\name}{@latex($name)}
\newcommand{\department}{@latex($department)}

\newcommand{\dateStart}{@latex($dateStart)}
\newcommand{\placeStart}{@latex($placeStart)}
\newcommand{\timeStart}{@latex($timeStart)}

\newcommand{\place}{@latex($place)}

\newcommand{\dateEnd}{@latex($dateEnd)}
\newcommand{\placeEnd}{@latex($placeEnd)}
\newcommand{\timeEnd}{@latex($timeEnd)}

\newcommand{\sppSymbol}{@latex($sppSymbol)}
\newcommand{\transportation}{@latex($transportation)}

\newcommand{\travellingExpense}{@latex($travellingExpense)}
\newcommand{\accommodationExpense}{@latex($accommodationExpense)}
\newcommand{\mealsReimbursement}{@latex($mealsReimbursement)}
\newcommand{\participationExpense}{@latex($participationExpense)}
\newcommand{\otherExpenses}{@latex($otherExpenses)}

\newcommand{\conclusion}{@latex($conclusion)}
\newcommand{\iban}{@latex($iban)}
\newcommand{\address}{@latex($address)}


\begin{document}
	{\Large\bf SPRÁVA Z TUZEMSKEJ PRACOVNEJ CESTY} \hfill {\large [\sofiaID]}

	\def\arraystretch{1.75}
	\begin{table}[h!]
		\centering
		\begin{tabularx}{\linewidth}{|p{0.3\linewidth}|XXX|}
			\hline
			Meno a priezvisko zamestnanca: & \multicolumn{3}{L{0.64\linewidth}|}{\name} \\ \hline
			Pracovisko -- skratka katedry: & \multicolumn{3}{L{0.64\linewidth}|}{\department} \\ \hline
			Začiatok cesty, dátum: & \multicolumn{1}{L{0.255\linewidth}|}{\dateStart, \placeStart} & \multicolumn{1}{l|}{Čas:} & \timeStart \\ \hline
			Miesto: & \multicolumn{3}{L{0.64\linewidth}|}{\place} \\ \hline
			Koniec cesty, dátum: & \multicolumn{1}{L{0.255\linewidth}|}{\dateEnd, \placeEnd} & \multicolumn{1}{l|}{Čas:} & \timeEnd \\ \hline
			\textbf{ÚHRADA Z ŠPP:} & \multicolumn{1}{L{0.255\linewidth}|}{\sppSymbol} & \multicolumn{1}{l|}{Dopravný prostriedok:} & \multicolumn{1}{L{0.135\linewidth}|}{\transportation} \\ \hline
		\end{tabularx}
	\end{table}

	{\large\bf VYÚČTOVANIE NÁKLADOV}
	\vspace*{-0.5em}
	\begin{table}[h!]
		\begin{tabularx}{\linewidth}{|X|>{\centering\arraybackslash}p{5cm}|}
			\hline
			& \textbf{suma v EUR} \\ \hline
			Cestovné / 04: & \travellingExpense \\ \hline
			Ubytovanie / 01: & \accommodationExpense \\ \hline
			Stravné: (uvádzať len ak je iné ako zákon stanovuje) & \mealsReimbursement \\ \hline
			Vložné / 05: & \participationExpense \\ \hline
			Iné výdavky / 03: & \otherExpenses \\ \hline
			\rule{0pt}{1.6em}{\bf NÁKLADY NA CESTU:}\rule{0pt}{1.6em} & \\ \hline
		\end{tabularx}
	\end{table}

	\begin{table}[h!]
		\begin{tabularx}{\linewidth}{|X|}
			\hline
			\textbf{\underline{Výsledky cesty:}} \\
			\conclusion
			\vspace*{2em} \\ \hline
		\end{tabularx}
	\end{table}

	\vspace*{-1em}

	\begin{table}[h!]
		\centering
		\begin{tabularx}{\linewidth}{|lXXX|}
			\hline
			\multicolumn{4}{|l|}{\bf PRE iné ÚČTY} \\ \hline
			\multicolumn{1}{|l|}{IBAN vašej banky:} & \multicolumn{1}{X|}{} & \multicolumn{1}{l|}{swift:} &  \\ \hline
			\multicolumn{2}{|l|}{\underline\bf ADRESA BANKY:} & \multicolumn{2}{l|}{\underline\bf VAŠA ADRESA:} \\ \hline
			\multicolumn{2}{|l|}{} & \multicolumn{2}{L{0.4\linewidth}|}{\address} \\ \hline
			\multicolumn{4}{|l|}{\bf PRE EURO} \\ \hline
			\multicolumn{1}{|l|}{\bf Číslo vášho účtu -- kód banky:} & \multicolumn{3}{l|}{\iban} \\ \hline
		\end{tabularx}
	\end{table}

	\begin{flushright}
		\textbf{Podpis účastníka:} \hspace*{3em} \makebox[2in]{\hrulefill}
	\end{flushright}

    \pagebreak

    @if($mealsReimbursementBool && $notReimbursedMeals !== null && $notReimbursedMeals != '')

    {\large\bf Zrážky zo stravného}

    Vyberte, prosím, ktoré jedlá si \textbf{nežiadate} preplatiť.

    @php
        $meals = $notReimbursedMeals;
        $currentDate = $mealsStart;
    @endphp

    \begin{table}[h!]
    \centering
    \begin{tabular}{|C{5em}|C{5em}|C{5em}|C{5em}|}
    \hline
    \bf Dátum & \bf Raňajky & \bf Obed & \bf Večera \\
    \hline
    @for ($i = 0; $i < $days; $i++)
        @latex($currentDate->format('d.m.'))
            & {!! ($meals[$i * 3] === '1') ? '\checkmark' : '' !!}
            & {!! ($meals[$i * 3 + 1] === '1') ? '\checkmark' : '' !!}
            & {!! ($meals[$i * 3 + 2] === '1') ? '\checkmark' : '' !!} \\ \hline
        @php $currentDate->modify('+1 day'); @endphp
    @endfor
    \end{tabular}
    \end{table}

    @endif

\end{document}

