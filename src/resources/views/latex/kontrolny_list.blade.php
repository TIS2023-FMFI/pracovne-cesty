\documentclass[10pt,a4paper]{article}

\usepackage[utf8]{inputenc}
\usepackage[T1]{fontenc}
\usepackage{geometry}
\usepackage{lmodern}
\usepackage{array}
\usepackage{tabularx}
\usepackage{graphicx}
\usepackage{setspace}
\usepackage{amssymb}

\geometry{a4paper,left=15mm,right=15mm,top=15mm,bottom=15mm}
\setlength\parindent{0pt}

\renewcommand{\familydefault}{lmss}
\pagenumbering{gobble}

\newcommand{\placeholder}[1]{< #1 >}

\newcommand{\amount}{@latex($amount)}
\newcommand{\sppSymbol}{@latex($sppSymbol)}
\newcommand{\source}{@latex($source)}
\newcommand{\functionalRegion}{@latex($functionalRegion)}
\newcommand{\financialCentre}{@latex($financialCentre)}
\newcommand{\purposeDetails}{@latex($purposeDetails)}
\newcommand{\daiChair}{@latex($daiChair)}
\newcommand{\finDirector}{@latex($finDirector)}
\newcommand{\secretary}{@latex($secretary)}
\newcommand{\PI}{@latex($pi)}

\begin{document}
\begin{table}[h!]
\centering
\begin{tabular}{p{0.475\linewidth} p{0.48\linewidth}}
	\includegraphics[width=0.475\linewidth]{<?= public_path('images/uk_logo.png') ?>} & \parbox[t]{\linewidth}{\raggedleft \vspace*{-3.5em} Príloha č. 1 k vnútornému predpisu č. 1/2023} \\
\end{tabular}
\end{table}

\vspace*{-1em}

{\large\bf KONTROLNÝ LIST}

na vykonanie základnej finančnej kontroly v zmysle § 7 zákona č. 357/2015 Z. z. o finančnej kontrole a audite a o zmene a doplnení niektorých zákonov v znení neskorších predpisov

\vspace*{0.5em}

{\bf Požiadavka na zabezpečenie dodávky tovaru, prác a služieb}
\vspace*{-1em}

\def\arraystretch{1.5}
\begin{table}[h!]
\centering
\begin{tabular}{|p{0.975\linewidth}|}
	\hline

	\textbf{Vecne príslušný organizačný útvar:} \\ \hline

	\textbf{Zamestnanec (meno a priezvisko, podpis):} \\ \hline

	\begin{tabular}[c]{@{}p{\linewidth}@{}}
		\textbf{Popis požadovaného tovaru, prác a služieb a zdôvodnenie požiadavky:}

		\purposeDetails
		\vspace{2em}

		\textbf{Zdroj/finančné krytie:} \sppSymbol
	\end{tabular} \\ \hline

	\textbf{Predpokladaná cena vrátane DPH v Eur:} \amount \\ \hline

	\begin{tabular}[c]{@{}p{\linewidth}@{}}
		\textbf{Dodávateľ tovaru, prác a služieb (názov/meno, adresa, IČO, bankové spojenie), ak je známy:}
		\vspace*{1em}
	\end{tabular} \\ \hline

	\begin{tabular}[c]{@{}p{\linewidth}@{}}
		\textbf{V prípade osobitne určeného zdroja financovania (projekty  EÚ, ostatné projekty, granty...) -- finančná operácia je / nie je}\textsuperscript{*)} \textbf{v súlade s:} \\
	\end{tabular} \\ \hline

	\begin{tabular}[c]{@{}p{\linewidth}@{}}
		a) Zmluva o poskytnutí  NFP – názov/ITMS kód:

		b) Iná zmluva/projekt č.:

        \hangindent=0.822\linewidth
        \hangafter=1
		\textit{Dátum:} \hspace{2cm} \textit{Meno a priezvisko, podpis zodpovedného riešiteľa/finančného manažéra:} \PI
	\end{tabular} \\ \hline

	\begin{tabular}[c]{@{}p{\linewidth}@{}}
		\textit{Finančná operácia \textbf{je / nie je}\textsuperscript{*)} v súlade s činnosťou organizačného útvaru a \textbf{je / nie je}\textsuperscript{*)} možné v nej pokračovať. Základnú finančnú kontrolu vykonal:}

        \hangindent=0.625\linewidth
        \hangafter=1
		\textit{Dátum:} \hspace{2cm} \textit{Meno a priezvisko, podpis vedúceho zamestnanca:} \daiChair
	\end{tabular} \\ \hline
\end{tabular}
\end{table}

\vspace*{-0.5em}
\textbf{Dispozície pre VO:}
\vspace*{-1em}

\begin{table}[h!]
\centering
\begin{tabular}{|p{0.975\linewidth}|}
	\hline
	\begin{tabular}[c]{@{}p{0.5\linewidth}p{0.5\linewidth}@{}}
		\textbf{Číslo zmluvy:} & \textbf{Skupina materiálu:} \\
	\end{tabular}
	\textit{Finančná operácia \textbf{je / nie je}\textsuperscript{*)} v súlade so zákonom č. 343/2015 Z. z. o verejnom obstarávaní v znení neskorších predpisov a \textbf{je / nie je}\textsuperscript{*)} možné v nej pokračovať. Základnú finančnú kontrolu vykonal:}

	\textit{Dátum:} \hspace{2cm} \textit{Meno a priezvisko, podpis:} \\
	\hline
\end{tabular}
\end{table}

\vspace*{-0.5em}
\textbf{Rozpočtové dispozície:}
\vspace*{-1em}
\begin{table}[h!]
\centering
\begin{tabularx}{\linewidth}{|X|X|l|X|X|}
	\hline
	\textbf{Zdroj} & \textbf{Funkčná oblasť} & \textbf{Ekonomická klasifikácia} & \textbf{Prvok ŠPP} & \textbf{Fin. stredisko} \\ \hline
	\source & \functionalRegion & & \sppSymbol & \financialCentre \\ \hline
	& & & & \\ \hline
	& & & & \\ \hline
\end{tabularx}
\begin{tabular}{|p{0.975\linewidth}|}
	\textit{Finančná operácia \textbf{je / nie je}\textsuperscript{*)} v súlade s rozpočtom organizačnej jednotky UK a \textbf{je / nie je}\textsuperscript{*)} možné v nej pokračovať. Základnú finančnú kontrolu vykonal:}

	\textit{Dátum:} \hspace{2cm} \textit{Meno a priezvisko, podpis:} \finDirector \\ \hline

	\textit{\textbf{Účtovné dispozície (nepovinné):}}

	\vspace*{1em}

	\textit{Dátum:} \hspace{2cm} \textit{Meno a priezvisko, podpis:} \\ \hline
\end{tabular}
\end{table}

\vspace*{-0.5em}
\textbf{Vyjadrenie vedúceho zamestnanca:}
\vspace*{-1em}

\begin{table}[h!]
\centering
\begin{tabular}{|p{0.975\linewidth}|}
	\hline
	\textit{Finančná operácia \textbf{je / nie je}\textsuperscript{*)} v súlade s platnými internými aktmi riadenia na UK a v súlade so zásadami hospodárnosti, efektívnosti, účelnosti a účinnosti pri nakladaní s verejnými prostriedkami a \textbf{je / nie je}\textsuperscript{*)} možné ju vykonať.}

	\textit{Dátum:} \hspace{2cm} \textit{Meno a priezvisko, podpis:} \secretary \\ \hline
\end{tabular}
\end{table}
\vspace*{-0.5em}

\textsuperscript{*)} nehodiace sa prečiarknite
\end{document}
