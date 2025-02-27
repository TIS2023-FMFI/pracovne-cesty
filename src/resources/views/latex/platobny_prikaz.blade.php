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
\usepackage{amssymb}

\geometry{a4paper,left=15mm,right=15mm,top=15mm,bottom=15mm}
\setlength\parindent{0pt}

\renewcommand{\familydefault}{lmss}
\pagenumbering{gobble}

\newcolumntype{C}{>{\centering\arraybackslash}X}

\newcommand{\placeholder}[1]{< #1 >}

\newcommand{\amount}{@latex($amount)}
\newcommand{\grantee}{@latex($grantee)}
\newcommand{\address}{@latex($address)}
\newcommand{\source}{@latex($source)}
\newcommand{\functionalRegion}{@latex($functionalRegion)}
\newcommand{\sppSymbol}{@latex($sppSymbol)}
\newcommand{\financialCentre}{@latex($financialCentre)}
\newcommand{\iban}{@latex($iban)}

\begin{document}
\begin{table}[h]
\centering
\begin{tabularx}{\linewidth}{|p{6em}|p{0.5\linewidth}|>{\centering\arraybackslash}X|}
	\hline
	\includegraphics[width=\linewidth]{<?= public_path('images/uk_logo_square.png') ?>} & {
		\vspace*{-5em}
		\textbf{Univerzita Komenského v Bratislave}

		\textbf{Rektorát/Fakulta matematiky, fyziky a informatiky,}

		\textbf{Mlynská dolina, 842 48 Bratislava}
	} & \vspace*{-3.5em}\textbf{Platobný príkaz} \\ \hline
\end{tabularx}
\end{table}

Číslo: \hspace*{0.27\linewidth} Za účet: \textbf{dotačnej činnosti/nedotačnej činnosti/podnikateľskej činnosti*}

Vystavený organizačným útvarom RUK/fakulty:

\bgroup
\def\arraystretch{2}
\begin{table}[h]
\centering
\begin{tabularx}{\linewidth}{|X|}
	\hline
	\textbf{Predmet:} \\ \hline
	\textbf{Suma:} \amount \\
	\textbf{Slovom:} \\ \hline
	\textbf{Vyplatiť komu:} \grantee \\
	\textbf{Adresa:} \address \\ \hline
\end{tabularx}
\end{table}
\egroup

\vspace*{-0.5em}
\begin{spacing}{1.5}
	\textbf{Vyplatiť prevodom z účtu / v hotovosti*:}

	\textbf{Na účet / v pokladni * RUK/fakulty:} \iban

	\textbf{Dátum splatnosti:}

	\textbf{Správa pre prijímateľa:}
\end{spacing}

\bgroup
\def\arraystretch{2}
\begin{table}[h]
\centering
\begin{tabularx}{\linewidth}{|X|}
	\hline
	Finančná operácia \textbf{je / nie je}* v súlade s činnosťou organizačného útvaru a \textbf{je / nie je}* možné v nej pokračovať. Základnú finančnú kontrolu vykonal:

	\vspace*{1em}

	Dátum: \hspace{2cm} Meno a priezvisko ved. zamestnanca: \hspace{3.4cm} Podpis:  	\vspace*{1em}\\ \hline
\end{tabularx}
\end{table}
\egroup

\vspace*{-0.5em}
\textbf{\textit{Rozpočtové dispozície:}}
\vspace*{-0.5em}

\begin{table}[h!]
\centering
\bgroup
\def\arraystretch{1.5}
\begin{tabularx}{\linewidth}{|c|c|c|C|C|C|}
	\hline
	\textbf{Zdroj} & \textbf{Funkčná oblasť} & \textbf{Podprogram} & \textbf{Ekonom. klasifik.} & \textbf{ŠPP} & \textbf{Finančné stredisko} \\ \hline
	\source & \functionalRegion & & & \sppSymbol & \financialCentre \\ \hline
\end{tabularx}
\egroup

\bgroup
\def\arraystretch{2}
\begin{tabularx}{\linewidth}{|X|}
	Finančná operácia \textbf{je / nie je}* v súlade s rozpočtom organizačnej jednotky/projektu a \textbf{je / nie je}* možné v nej pokračovať. Základnú finančnú kontrolu vykonal:

	\vspace*{1em}

	Dátum: \hspace{2cm} Meno a priezvisko zodpovedného zamestnanca: \hspace{1.95cm} Podpis: \vspace*{2em} \\ \hline
\end{tabularx}
\egroup
\end{table}

\vspace*{-0.5em}
\textbf{\textit{Vyjadrenie vedúceho zamestnanca:}}
\vspace*{-0.5em}

\bgroup
\def\arraystretch{2}
\begin{table}[h!]
\centering
\begin{tabularx}{\linewidth}{|X|}
	\hline
	Finančná operácia \textbf{je / nie je}* v súlade s §7 zákona č. 357/2015 Z.z. o finančnej kontrole a audite a o zmene a doplnení niektorých zákonov v znení neskorších predpisov a \textbf{je / nie je}* možné ju vykonať.

	\vspace*{1em}

	Dátum: \hspace{2cm} Meno a priezvisko: \hspace{6.16cm} Podpis: \vspace*{1em}\\ \hline
\end{tabularx}
\end{table}
\egroup

* nehodiace sa prečiarknite

\vspace*{-0.5em}
\rule{\linewidth}{0.4pt}

\bgroup
\def\arraystretch{2}
\begin{table}[h!]
\begin{tabular}{|l|p{0.4\linewidth}|}
	\hline
	Likvidoval dňa: & \\ \hline
	Meno a priezvisko: & \\ \hline
	Podpis: & \\ \hline
\end{tabular} \hspace{0.066\linewidth}
\begin{tabular}{|p{0.3\linewidth}|}
	\hline
	Hrubá suma \\ \hline
	Daň \\ \hline
	Čistá suma \\ \hline
\end{tabular} \quad
\end{table}
\egroup

\end{document}
