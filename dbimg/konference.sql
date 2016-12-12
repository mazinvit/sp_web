-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Ned 11. pro 2016, 14:27
-- Verze serveru: 10.1.16-MariaDB
-- Verze PHP: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `konference`
--
CREATE DATABASE IF NOT EXISTS `konference` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `konference`;

-- --------------------------------------------------------

--
-- Zástupná struktura pro pohled `all_articles`
--
DROP VIEW IF EXISTS `all_articles`;
CREATE TABLE `all_articles` (
`id` int(11)
,`nazev` varchar(45)
,`autori` mediumtext
,`abstract` text
,`pdf` varchar(255)
,`id_uzivatel` int(11)
,`schvaleno` int(11)
,`hodnoceni` decimal(39,4)
);

-- --------------------------------------------------------

--
-- Struktura tabulky `prispevky`
--

DROP TABLE IF EXISTS `prispevky`;
CREATE TABLE `prispevky` (
  `id` int(11) NOT NULL,
  `nazev` varchar(45) NOT NULL,
  `autori` mediumtext NOT NULL,
  `abstract` text NOT NULL,
  `pdf` varchar(255) NOT NULL,
  `id_uzivatel` int(11) NOT NULL,
  `schvaleno` int(11) NOT NULL,
  `prumer_hodnoc` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `prispevky`
--

INSERT INTO `prispevky` (`id`, `nazev`, `autori`, `abstract`, `pdf`, `id_uzivatel`, `schvaleno`, `prumer_hodnoc`) VALUES
(1, 'Nebezpečné soutěže', 'Karel Omáčka', 'Se soutěžemi, ve kterých se podvodníci vydávají za zástupce společnosti Seznam.cz, se roztrhnul pytel. Lidé pro získání ceny musí zaslat prémiovou SMS, jež je vyjde klidně i na 100 Kč. Ve skutečnosti ale žádnou výhru nezískají, protože jde o podvod. V dalších soutěžích se kyberzločinci zase snaží vylákat od důvěřivců osobní údaje.', 'souteze.pdf', 3, 0, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `recenze`
--

DROP TABLE IF EXISTS `recenze`;
CREATE TABLE `recenze` (
  `id_uzivatel` int(11) NOT NULL,
  `id_prispevek` int(11) NOT NULL,
  `originalita` int(11) NOT NULL,
  `tema` int(11) NOT NULL,
  `pravopis` int(11) NOT NULL,
  `srozumitelnost` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `recenze`
--

INSERT INTO `recenze` (`id_uzivatel`, `id_prispevek`, `originalita`, `tema`, `pravopis`, `srozumitelnost`) VALUES
(2, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `uzivatele`
--

DROP TABLE IF EXISTS `uzivatele`;
CREATE TABLE `uzivatele` (
  `id` int(11) NOT NULL,
  `login` varchar(45) NOT NULL,
  `pass` mediumtext NOT NULL,
  `jmeno` mediumtext NOT NULL,
  `prava` int(11) NOT NULL,
  `email` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `uzivatele`
--

INSERT INTO `uzivatele` (`id`, `login`, `pass`, `jmeno`, `prava`, `email`) VALUES
(1, 'admin', '6e017b5464f820a6c1bb5e9f6d711a667a80d8ea', 'Vít Mazín', 1, 'vit.mazin@seznam.cz'),
(2, 'novak', '7e240de74fb1ed08fa08d38063f6a6a91462a815', 'Jan Novák', 2, 'novak@jan.cz'),
(3, 'omacka', '7e240de74fb1ed08fa08d38063f6a6a91462a815', 'Karel Omáčka', 3, 'omacka@seznam.cz');

-- --------------------------------------------------------

--
-- Struktura pro pohled `all_articles`
--
DROP TABLE IF EXISTS `all_articles`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `all_articles`  AS  select `p`.`id` AS `id`,`p`.`nazev` AS `nazev`,`p`.`autori` AS `autori`,`p`.`abstract` AS `abstract`,`p`.`pdf` AS `pdf`,`p`.`id_uzivatel` AS `id_uzivatel`,`p`.`schvaleno` AS `schvaleno`,(sum((((`r`.`originalita` + `r`.`tema`) + `r`.`pravopis`) + `r`.`srozumitelnost`)) / (count(0) * 4)) AS `hodnoceni` from (`prispevky` `p` join `recenze` `r`) where ((`p`.`schvaleno` = 1) and (`p`.`id` = `r`.`id_prispevek`)) ;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `prispevky`
--
ALTER TABLE `prispevky`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_prispevky_uzivatele_idx` (`id_uzivatel`);

--
-- Klíče pro tabulku `recenze`
--
ALTER TABLE `recenze`
  ADD PRIMARY KEY (`id_uzivatel`,`id_prispevek`),
  ADD KEY `fk_recenze_prispevky1_idx` (`id_prispevek`);

--
-- Klíče pro tabulku `uzivatele`
--
ALTER TABLE `uzivatele`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `prispevky`
--
ALTER TABLE `prispevky`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `uzivatele`
--
ALTER TABLE `uzivatele`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `prispevky`
--
ALTER TABLE `prispevky`
  ADD CONSTRAINT `fk_prispevky_uzivatele` FOREIGN KEY (`id_uzivatel`) REFERENCES `uzivatele` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `recenze`
--
ALTER TABLE `recenze`
  ADD CONSTRAINT `fk_recenze_prispevky1` FOREIGN KEY (`id_prispevek`) REFERENCES `prispevky` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_recenze_uzivatele1` FOREIGN KEY (`id_uzivatel`) REFERENCES `uzivatele` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
