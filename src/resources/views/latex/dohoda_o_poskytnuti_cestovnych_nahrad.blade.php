\documentclass[10pt,a4paper]{article}

\usepackage[utf8]{inputenc}
\usepackage[T1]{fontenc}
\usepackage{geometry}
\usepackage{lmodern}
\usepackage{array}
\usepackage{tabularx}
\usepackage{multirow}
\usepackage{graphicx}
\usepackage{setspace}
\usepackage{float}

\geometry{a4paper,left=20mm,right=20mm,top=20mm,bottom=20mm}
\setlength\parindent{0pt}

\renewcommand{\familydefault}{lmss}
\pagenumbering{gobble}

\newcommand{\placeholder}[1]{< #1 >}

\newcommand{\firstName}{@latex($firstName)}
\newcommand{\lastName}{@latex($lastName)}
\newcommand{\academicDegree}{@latex($academicDegree)}
\newcommand{\address}{@latex($address)}
\newcommand{\contributionA}{@latex($contributionA)}
\newcommand{\contributionB}{@latex($contributionB)}
\newcommand{\contributionC}{@latex($contributionC)}
\newcommand{\department}{@latex($department)}
\newcommand{\place}{@latex($place)}
\newcommand{\datetimeStart}{@latex($datetimeStart)}
\newcommand{\datetimeEnd}{@latex($datetimeEnd)}
\newcommand{\transportation}{@latex($transportation)}
\newcommand{\tripPurpose}{@latex($tripPurpose)}
\newcommand{\fund}{@latex($fund)}
\newcommand{\functionalRegion}{@latex($functionalRegion)}
\newcommand{\financialCentre}{@latex($financialCentre)}
\newcommand{\sppSymbol}{@latex($sppSymbol)}
\newcommand{\account}{@latex($account)}
\newcommand{\grantee}{@latex($grantee)}
\newcommand{\iban}{@latex($iban)}
\newcommand{\incumbentNameA}{@latex($incumbentNameA)}
\newcommand{\incumbentNameB}{@latex($incumbentNameB)}
\newcommand{\positionNameA}{@latex($positionNameA)}
\newcommand{\positionNameB}{@latex($positionNameB)}
\newcommand{\contributionAText}{@latex($contributionAText)}
\newcommand{\contributionBText}{@latex($contributionBText)}
\newcommand{\contributionCText}{@latex($contributionCText)}

\begin{document}
\begin{center}
	{\LARGE\bf Dohoda o poskytnutí cestovných náhrad}
	\vspace*{1em}

	uzatvorená podľa § 51 Občianskeho zákonníka a zákona o cestovných náhradách
\end{center}

\vspace*{1em}
\textbf{\textit{Poskytovateľ náhrady:}}
\vspace*{1em}

Univerzita Komenského v Bratislave

Fakulta matematiky, fyziky a informatiky

Mlynská dolina

842 48 Bratislava

\vspace*{1em}
\textbf{zastúpená:} \incumbentNameA \; -- \; \positionNameA

\textbf{v zmluvných veciach koná:} \incumbentNameB \; -- \; \positionNameB

\vspace*{-1em}
\begin{center}
	\textbf{a}
\end{center}
\vspace*{-0.5em}

\textbf{\textit{Príjemca náhrady/Vycestovaný (á):}}
\vspace*{-1em}

\def\arraystretch{1.2}
\begin{table}[h!]
\centering
\begin{tabular}{|p{0.224\linewidth}|p{0.724\linewidth}|}
	\hline
	Meno, priezvisko, titul: & \firstName \lastName, \academicDegree \\ \hline
	Adresa: & \address \\
	\hline
\end{tabular}
\end{table}

\vspace*{-1em}
\begin{center}
	\textbf{uzatvárajú túto dohodu o poskytnutí cestovných náhrad (ďalej „dohoda“)}
\end{center}

\textit{Predmetom dohody je poskytnutie cestovných náhrad v zmysle zákona č. 283/2002 Z.z. o cestovných náhradách v platnom znení.}

\begin{table}[h!]
\centering
\begin{tabular}{|p{0.224\linewidth}|p{0.35\linewidth}|p{0.35\linewidth}|}
	\hline
	\textbf{Katedra:} & \multicolumn{2}{l|}{\department} \\ \hline
	\textbf{Štát a miesto služobnej cesty:} & \multicolumn{2}{l|}{\place} \\ \hline
	\textbf{Dátum služobnej cesty:} & \multicolumn{1}{l|}{Začiatok: \datetimeStart} & Koniec: \datetimeEnd \\ \hline
	\textbf{Dopravný prostriedok:} & \multicolumn{2}{l|}{\transportation} \\ \hline
	\textbf{Účel pracovnej cesty:} & \multicolumn{2}{l|}{\tripPurpose} \\ \hline
	\multicolumn{1}{|p{0.224\linewidth}|}{\multirow{3}{*}{
		\begin{tabular}[c]{@{}>{\centering\arraybackslash}p{\linewidth}@{}}
			\textbf{Prínos pre pracovisko fakulty, poznatky sa využijú:}

			(označiť skutočný prínos)
		\end{tabular}
	}} & \multicolumn{1}{p{0.35\linewidth}|}{pre vedecký výskum} & \contributionA \contributionAText \\ \cline{2-3}
	\multicolumn{1}{|c|}{} & \multicolumn{1}{p{0.35\linewidth}|}{na pedagogickú činnosť} & \contributionB \contributionBText \\ \cline{2-3}
	\multicolumn{1}{|c|}{} & \multicolumn{1}{p{0.35\linewidth}|}{na prezentáciu \vspace*{1em}} & \contributionC \contributionCText \\ \hline
\end{tabular}
\end{table}

{\Large\bf Hradené z úlohy číslo:}

\vspace*{-0.5em}
\begin{table}[h!]
\centering
\begin{tabularx}{\linewidth}{|c|c|c|>{\centering\arraybackslash}X|c|>{\centering\arraybackslash}X|}
	\hline
	\textbf{FOND} & \textbf{FO} & \textbf{FIN. STREDISKO} & \textbf{ŠPP PRVOK} & \textbf{Rozpočtová položka*} & \textbf{Zodpovedný riešiteľ} \\ \hline
	\fund & \functionalRegion & \financialCentre & \sppSymbol & \account & \grantee \\ \hline
\end{tabularx}
\end{table}
\vspace*{-1em}

* vyberte: \textbf{631 001} Tuzemské SC alebo \textbf{631 002} Zahraničné SC

\textbf{\textit{Výplatu preveďte:} bankovým prevodom}

\vspace*{-0.5em}
\begin{table}[h!]
\centering
\begin{tabularx}{\linewidth}{|X|X|}
	\hline
	\textbf{číslo účtu vo forme IBAN} & \textbf{názov banky} \\ \hline
	\iban & \\ \hline
\end{tabularx}
\end{table}
\vspace*{-0.5em}

Príjemca náhrady \textbf{je povinný do 10 dní od uskutočnenia pracovnej cesty} predložiť (zaslať) poskytovateľovi
náhrady originály dokladov preukazujúcich výšku opodstatnených nákladov, ktoré sú predmetom cestovných náhrad
v zmysle tejto dohody, inak nebudú náhrady preplatené. \textbf{Dohoda musí byť uzavretá pred nástupom
na pracovnú cestu. Dohodu je potrebné priložiť pri odovzdávaní cestovného príkazu.}

\vspace*{1em}

V Bratislave, dňa:

\vspace*{4em}

\par\noindent\makebox[4cm]{\hrulefill} \hspace{6cm} \makebox[4cm]{\hrulefill}
\par\noindent\makebox[4cm][l]{Príjemca/Vycestovaný(á)} \hspace{6cm} \makebox[4cm][l]{Poskytovateľ}

\end{document}
