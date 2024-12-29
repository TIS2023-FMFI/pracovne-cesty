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

\newcommand{\name}{@latex($name)}
\newcommand{\department}{@latex($department)}

\newcommand{\datetimeStart}{@latex($datetimeStart)}
\newcommand{\placeStart}{@latex($placeStart)}
\newcommand{\datetimeBorderCrossingStart}{@latex($datetimeBorderCrossingStart)}

\newcommand{\country}{@latex($country)}
\newcommand{\place}{@latex($place)}

\newcommand{\datetimeEnd}{@latex($datetimeEnd)}
\newcommand{\placeEnd}{@latex($placeEnd)}
\newcommand{\datetimeBorderCrossingEnd}{@latex($datetimeBorderCrossingEnd)}

\newcommand{\sppSymbol}{@latex($sppSymbol)}
\newcommand{\transportation}{@latex($transportation)}

\newcommand{\travellingExpenseForeign}{@latex($travellingExpenseForeign)}
\newcommand{\travellingExpense}{@latex($travellingExpense)}

\newcommand{\accommodationExpenseForeign}{@latex($accommodationExpenseForeign)}
\newcommand{\accommodationExpense}{@latex($accommodationExpense)}

\newcommand{\mealsReimbursementForeign}{@latex($mealsReimbursementForeign)}
\newcommand{\mealsReimbursement}{@latex($mealsReimbursement)}

\newcommand{\participationExpenseForeign}{@latex($participationExpenseForeign)}
\newcommand{\participationExpense}{@latex($participationExpense)}

\newcommand{\insuranceExpenseForeign}{@latex($insuranceExpenseForeign)}
\newcommand{\insuranceExpense}{@latex($insuranceExpense)}

\newcommand{\otherExpensesForeign}{@latex($otherExpensesForeign)}
\newcommand{\otherExpenses}{@latex($otherExpenses)}

\newcommand{\allowanceForeign}{@latex($allowanceForeign)}
\newcommand{\allowance}{@latex($allowance)}

\newcommand{\advanceExpenseForeign}{@latex($advanceExpenseForeign)}
\newcommand{\advanceExpense}{@latex($advanceExpense)}

\newcommand{\invitationCaseCharges}{@latex($invitationCaseCharges)}
\newcommand{\conclusion}{@latex($conclusion)}
\newcommand{\iban}{@latex($iban)}


\begin{document}
{\Large\bf SPRÁVA ZO ZAHRANIČNEJ PRACOVNEJ CESTY}

\def\arraystretch{1.25}
\begin{table}[h!]
\centering
\begin{tabularx}{\linewidth}{|p{0.3\linewidth}|XXX|}
	\hline
	Meno a priezvisko zamestnanca: & \multicolumn{3}{p{0.64\linewidth}|}{\name} \\ \hline
	Pracovisko -- skratka katedry: & \multicolumn{3}{p{0.64\linewidth}|}{\department} \\ \hline
	Začiatok cesty (čas odchodu,\par dátum): & \multicolumn{1}{p{0.2\linewidth}|}{\datetimeStart, \placeStart} & \multicolumn{1}{l|}{Čas prekročenia hraníc:} & \multicolumn{1}{p{0.135\linewidth}|}{\datetimeBorderCrossingStart} \\ \hline
	Navštívený štát: & \multicolumn{1}{X|}{\country} & \multicolumn{2}{p{0.4\linewidth}|}{Miesto: \place} \\ \hline
	Koniec cesty (čas príchodu,\par dátum): & \multicolumn{1}{p{0.2\linewidth}|}{\datetimeEnd, \placeEnd} & \multicolumn{1}{l|}{Čas prekročenia hraníc späť:} & \multicolumn{1}{p{0.135\linewidth}|}{\datetimeBorderCrossingEnd} \\ \hline
	\textbf{ÚHRADA Z FONDU:} & \multicolumn{1}{X|}{\sppSymbol} & \multicolumn{1}{l|}{Dopravný prostriedok:} & \multicolumn{1}{p{0.135\linewidth}|}{\transportation} \\ \hline
\end{tabularx}
\end{table}

{\large\bf VYÚČTOVANIE NÁKLADOV}
\vspace*{-0.5em}
\begin{table}[h!]
\begin{tabularx}{\linewidth}{|l|X|X|}
	\hline
	& \textbf{suma v cudzej mene} & \textbf{suma v EUR} \\ \hline
	Cestovné / 04: & \travellingExpenseForeign & \travellingExpense \\ \hline
	Ubytovanie / 01: & \accommodationExpenseForeign & \accommodationExpense \\ \hline
	Stravné: (uvádzať len ak je iné ako zákon stanovuje) & \mealsReimbursementForeign & \mealsReimbursement \\ \hline
	Vložné / 05: & \participationExpenseForeign & \participationExpense \\ \hline
	Poistenie / 02: & \insuranceExpenseForeign & \insuranceExpense \\ \hline
	Iné výdavky / 03: & \otherExpensesForeign & \otherExpenses \\ \hline
	Vreckové: (uvádzať len ak je iné ako zákon stanovuje) & \allowanceForeign & \allowance \\ \hline
	\textbf{Záloha na cestu:} & \advanceExpenseForeign & \advanceExpense \\ \hline
	\rule{0pt}{1.6em}{\large\bf NÁKLADY NA CESTU:}\rule{0pt}{1.6em} & & \\ \hline
\end{tabularx}
\end{table}

\bgroup
\def\arraystretch{1.5}
\begin{table}[h!]
\begin{tabularx}{\linewidth}{|X|}
	\hline
	\textcolor{red}{\textbf{V prípade pozvania druhou stranou vyčíslite/odhadnite preplatené výdavky:}} \invitationCaseCharges \\ \hline
\end{tabularx}
\end{table}
\egroup

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
\begin{tabularx}{\linewidth}{|lX|}
	\hline
	\multicolumn{2}{|l|}{\textbf{Účet vysielaného pracovníka}} \\ \hline
	\multicolumn{1}{|l|}{číslo účtu -- kód banky:} &  \\ \hline
	\multicolumn{1}{|l|}{\textbf{IBAN: (od 1.2.2014)}} & \iban \\ \hline
\end{tabularx}
\end{table}

\begin{center}
	\textbf{Podpis účastníka:}
\end{center}

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
