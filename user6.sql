-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 06 2017 г., 17:03
-- Версия сервера: 5.5.53
-- Версия PHP: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `user6`
--

-- --------------------------------------------------------

--
-- Структура таблицы `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_room` int(11) NOT NULL,
  `description` text NOT NULL,
  `time_start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `id_parent` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `events`
--

INSERT INTO `events` (`id`, `id_user`, `id_room`, `description`, `time_start`, `time_end`, `id_parent`, `create_time`) VALUES
(2, 5, 1, 'dcvxccvcxv', '2017-11-01 23:00:00', '2017-11-02 00:00:00', NULL, '2017-11-02 10:34:53'),
(3, 5, 1, 'dsffdsfdsf', '2017-11-02 07:00:00', '2017-11-02 08:00:00', NULL, '2017-11-02 10:43:08'),
(4, 22, 1, 'sdasdasd', '2017-11-03 06:00:00', '2017-11-03 08:00:00', NULL, '2017-11-03 07:01:55'),
(5, 22, 1, 'sdasdasd', '2017-11-03 09:00:00', '2017-11-03 10:00:00', NULL, '2017-11-03 07:02:15'),
(6, 1, 2, 'sadfasdasdasdasd', '2017-11-03 00:00:00', '2017-11-03 01:00:00', NULL, '2017-11-03 07:08:38'),
(7, 1, 3, 'sxcascsac', '2017-11-01 09:00:00', '2017-11-01 12:00:00', NULL, '2017-11-03 07:09:18'),
(8, 1, 2, 'sadfsaffasf', '2017-11-30 07:00:00', '2017-11-30 11:00:00', NULL, '2017-11-03 11:06:30'),
(10, 1, 3, 'asdsdsadasd', '2017-10-18 00:00:00', '2017-10-18 01:00:00', NULL, '2017-11-03 11:07:18'),
(15, 1, 3, 'x a asdc as faffasfasf asfafs asf a fasffsf', '2017-11-06 16:00:00', '2017-11-06 17:30:00', NULL, '2017-11-05 16:23:23'),
(16, 1, 1, 'test first', '2017-11-06 06:00:00', '2017-11-06 06:30:00', NULL, '2017-11-05 20:31:22'),
(17, 1, 1, 'asdsadsad', '2017-11-06 07:00:00', '2017-11-06 07:30:00', NULL, '2017-11-05 20:37:57'),
(18, 1, 1, 'ascascsac', '2017-11-06 14:00:00', '2017-11-06 14:30:00', NULL, '2017-11-05 20:39:41'),
(19, 1, 1, 'SACASCSACAS', '2017-11-06 16:00:00', '2017-11-06 16:30:00', NULL, '2017-11-05 20:42:26'),
(71, 1, 2, 'weeeeekly 4', '2017-11-13 10:00:00', '2017-11-13 10:30:00', 70, '2017-11-06 02:09:27'),
(73, 1, 2, 'weeeeekly 4', '2017-11-27 10:00:00', '2017-11-27 10:30:00', 70, '2017-11-06 02:09:27'),
(78, 1, 2, 'ssssssssssssss s s s ', '2017-11-06 10:00:00', '2017-11-06 10:30:00', NULL, '2017-11-06 02:12:43'),
(79, 1, 2, 'ssssssssssssss s s s ', '2017-11-20 10:00:00', '2017-11-20 10:30:00', 78, '2017-11-06 02:12:43'),
(80, 1, 2, 'ssssssssssssss s s s ', '2017-12-04 10:00:00', '2017-12-04 10:30:00', 78, '2017-11-06 02:12:43'),
(81, 1, 1, 'паиаппаипаипаипаиаи', '2017-12-04 06:00:00', '2017-12-04 06:30:00', NULL, '2017-11-06 02:14:51'),
(82, 1, 1, 'паиаппаипаипаипаиаи', '2017-12-18 06:00:00', '2017-12-18 06:30:00', 81, '2017-11-06 02:14:51'),
(83, 1, 1, 'паиаппаипаипаипаиаи', '2018-01-01 06:00:00', '2018-01-01 06:30:00', 81, '2017-11-06 02:14:51');

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'user'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Структура таблицы `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `rooms`
--

INSERT INTO `rooms` (`id`, `name`) VALUES
(1, 'Boardroom 1'),
(2, 'Boardroom 2'),
(3, 'Boardroom 3');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `id_role` int(11) NOT NULL DEFAULT '1',
  `login` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL DEFAULT 'first_hash'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `id_role`, `login`, `pass`, `username`, `email`, `hash`) VALUES
(1, 2, 'admin', 'c3284d0f94606de1fd2af172aba15bf3', 'Name admin', 'admin@email.com', '7baa64e02dcf91eabb265f7be2e7d3d5'),
(3, 1, 'test', '2f7b52aacfbf6f44e13d27656ecb1f59', 'nametest', 'test@email.ru', '3a50ba2bce7d044e4393176615c39e82'),
(18, 1, 'test2', '2f7b52aacfbf6f44e13d27656ecb1f59', 'zxccxz sdsdsd', 'test2@email.ru', 'first_hash'),
(22, 1, 'test3', '2f7b52aacfbf6f44e13d27656ecb1f59', 'test Name', 'test3@email.ua', 'fe73ffdc938738ea2f52e70080c694ce');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `events_fk0` (`id_user`),
  ADD KEY `events_fk1` (`id_room`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `users_fk0` (`id_role`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;
--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_fk1` FOREIGN KEY (`id_room`) REFERENCES `rooms` (`id`);

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_fk0` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
