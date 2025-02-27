\documentclass[12pt,a4paper]{article}

\usepackage[utf8]{inputenc}
\usepackage[T1]{fontenc}
\usepackage{geometry}
\usepackage{array}
\usepackage{tabularx}
\usepackage{setspace}
\usepackage{amssymb}

\geometry{a4paper,left=25mm,right=25mm,top=25mm,bottom=25mm}

\renewcommand{\familydefault}{cmr}
\pagenumbering{gobble}

\newcommand{\placeholder}[1]{< #1 >}

\newcommand{\orderNumber}{@latex($orderNumber)}
\newcommand{\tripDuration}{@latex($tripDuration)}
\newcommand{\address}{@latex($address)}
\newcommand{\name}{@latex($name)}


\begin{document}
\vspace*{0.2cm}

\begin{center}
	\large{\bf Čestné vyhlásenie k zahraničnej pracovnej ceste}
\end{center}

\bgroup
\def\arraystretch{2}
\begin{table}[h]
\centering
\begin{tabular}{|p{0.49\linewidth}|p{0.455\linewidth}|}
	\hline
	Číslo príkazu na zahraničnú pracovnú cestu: & \orderNumber  \\ \hline
	Doba trvania:                               & \tripDuration \\ \hline
	Miesto pobytu:                              & \address 		\\ \hline
	Meno a priezvisko vyhlasovateľa:            & \name 		\\ \hline
\end{tabular}
\end{table}
\egroup

\vspace*{2.2cm}

{\setstretch{1.5}

Ja dolu podpísaný {\bf čestne vyhlasujem}, že {\bf odmietam, aby mi zamestnávateľ vyplatil} zákonný nárok preddavkov
na zahraničnú pracovnú cestu, ktorý je mi povinný poskytnúť zamestnávateľ Univerzita Komenského v Bratislave,
Fakulta matematiky, \linebreak fyziky a informatiky, v zmysle § 36 ods. 1 zákona č. 40/2009 Z.z.
(zákon o cestovných náhradách).

Písomné doklady potrebné k vyúčtovaniu náhrad predložím zamestnávateľovi \linebreak
v lehotách stanovených v § 36 ods. 7 citovaného zákona.

}

\vspace*{1cm}

V Bratislave dňa

\vspace*{1cm}

\par\hfill\noindent\makebox[2.5in]{\hrulefill}
\par\hfill\noindent\makebox[2.5in][c]{podpis zamestnanca}

\end{document}
