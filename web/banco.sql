-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 27-Mar-2018 às 19:29
-- Versão do servidor: 10.1.9-MariaDB
-- PHP Version: 5.5.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `automacao`
--
CREATE DATABASE IF NOT EXISTS `automacao` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `automacao`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `alarme`
--

DROP TABLE IF EXISTS `alarme`;
CREATE TABLE IF NOT EXISTS `alarme` (
  `data` datetime NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `alarme`
--

INSERT INTO `alarme` (`data`, `status`) VALUES
('0000-00-00 00:00:00', '1');

-- --------------------------------------------------------

--
-- Estrutura da tabela `central`
--

DROP TABLE IF EXISTS `central`;
CREATE TABLE IF NOT EXISTS `central` (
  `data` datetime NOT NULL,
  `nome` varchar(30) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `porta` int(2) NOT NULL,
  PRIMARY KEY (`data`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `central`
--

INSERT INTO `central` (`data`, `nome`, `ip`, `porta`) VALUES
('2018-03-27 16:21:22', 'alpha', '192.168.0.177', 80);

-- --------------------------------------------------------

--
-- Estrutura da tabela `comodos`
--

DROP TABLE IF EXISTS `comodos`;
CREATE TABLE IF NOT EXISTS `comodos` (
  `id_comodo` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(30) NOT NULL,
  `data` datetime NOT NULL,
  PRIMARY KEY (`id_comodo`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `comodos`
--

INSERT INTO `comodos` (`id_comodo`, `nome`, `data`) VALUES
(67, 'Quarto', '2018-03-01 16:29:22'),
(68, 'Sala', '2018-03-01 16:29:26'),
(70, 'Cozinha', '2018-03-01 16:30:09');

-- --------------------------------------------------------

--
-- Estrutura da tabela `itens`
--

DROP TABLE IF EXISTS `itens`;
CREATE TABLE IF NOT EXISTS `itens` (
  `data` datetime NOT NULL,
  `comodo` varchar(20) NOT NULL,
  `central` varchar(20) NOT NULL,
  `porta` varchar(5) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `type` varchar(20) NOT NULL,
  `acao` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `itens`
--

INSERT INTO `itens` (`data`, `comodo`, `central`, `porta`, `nome`, `type`, `acao`) VALUES
('2018-03-01 16:30:32', 'Quarto', '192.168.0.177', '10', 'Lampada Sanca', 'checkbox', 'lampada'),
('2018-03-01 16:31:05', 'Quarto', '192.168.0.177', '11', 'Sensor', 'dht11', 'sensor'),
('2018-03-02 11:52:04', 'Quarto', '192.168.0.177', '16', 'sensor fumaÃ§a', 'mq-2', 'sensor'),
('2018-03-05 09:21:27', 'Quarto', '192.168.0.177', '20', 'lamada 2', 'checkbox', 'lampada'),
('2018-03-27 16:23:10', 'Quarto', '192.168.0.177', '18', 'Tomada', 'checkbox', 'tomada');

-- --------------------------------------------------------

--
-- Estrutura da tabela `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valor` varchar(300) NOT NULL,
  `data` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `log`
--

INSERT INTO `log` (`id`, `valor`, `data`) VALUES
(1, 'select * from itens where acao = ''lampada'' ', '2018-03-27 16:12:27'),
(2, 'select * from itens where acao = ''lampada'' ', '2018-03-27 16:12:29'),
(3, 'INSERT INTO `central`(`data`, `nome`, `ip`, `porta`) VALUES (''2018-03-27 16:21:22'',''alpha'',''192.168.0.177'',''80'')', '2018-03-27 16:21:22'),
(4, 'INSERT INTO `itens`(`data`, `comodo`, `central`, `porta`, `nome`, `type`, `acao`)\r\n				VALUES (''2018-03-27 16:23:10'',''Quarto'',''192.168.0.177'',''18'',''Tomada'',''checkbox'',''tomada'')', '2018-03-27 16:23:10'),
(5, 'porta=18; acao=liga; central=192.168.0.177; ', '2018-03-27 16:23:17'),
(6, 'update parametro set valor=0 where parametro = ''alarme''', '2018-03-27 16:26:29');

-- --------------------------------------------------------

--
-- Estrutura da tabela `parametro`
--

DROP TABLE IF EXISTS `parametro`;
CREATE TABLE IF NOT EXISTS `parametro` (
  `parametro` varchar(20) NOT NULL,
  `valor` varchar(20) NOT NULL,
  PRIMARY KEY (`parametro`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `parametro`
--

INSERT INTO `parametro` (`parametro`, `valor`) VALUES
('alarme', '0'),
('termometroPrincipal', '192.168.0.177');

-- --------------------------------------------------------

--
-- Estrutura da tabela `portasoutput`
--

DROP TABLE IF EXISTS `portasoutput`;
CREATE TABLE IF NOT EXISTS `portasoutput` (
  `data` datetime NOT NULL,
  `numero` int(2) NOT NULL,
  `central` varchar(20) DEFAULT NULL,
  `acao` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `portasoutput`
--

INSERT INTO `portasoutput` (`data`, `numero`, `central`, `acao`) VALUES
('2018-03-27 16:12:27', 10, '192.168.0.177', 'desligar'),
('2018-03-27 16:12:27', 20, '192.168.0.177', 'desligar'),
('2018-03-27 16:12:29', 10, '192.168.0.177', 'liga'),
('2018-03-27 16:12:29', 20, '192.168.0.177', 'liga'),
('2018-03-27 16:23:17', 18, '192.168.0.177', 'liga');

-- --------------------------------------------------------

--
-- Estrutura da tabela `sensores`
--

DROP TABLE IF EXISTS `sensores`;
CREATE TABLE IF NOT EXISTS `sensores` (
  `data` datetime NOT NULL,
  `valor` varchar(30) NOT NULL,
  `central` varchar(15) NOT NULL,
  `porta` int(2) NOT NULL,
  `tipo` char(10) NOT NULL,
  PRIMARY KEY (`data`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `sensores`
--

INSERT INTO `sensores` (`data`, `valor`, `central`, `porta`, `tipo`) VALUES
('2018-03-27 14:14:14', 'dht11;25;99;', '192.168.0.177', 11, 'dht11'),
('2018-03-27 14:18:00', 'dht11;26;88', '192.168.0.177', 11, 'dht11'),
('2018-03-27 14:21:21', 'mq-2;152', '192.168.0.177', 16, 'mq-2');

-- --------------------------------------------------------

--
-- Estrutura da tabela `temperatura`
--

DROP TABLE IF EXISTS `temperatura`;
CREATE TABLE IF NOT EXISTS `temperatura` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` datetime NOT NULL,
  `temperatura` int(11) NOT NULL,
  `umidade` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
