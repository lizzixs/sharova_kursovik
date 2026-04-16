-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Апр 15 2026 г., 21:43
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `shift_drift`
--

-- --------------------------------------------------------

--
-- Структура таблицы `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `brands`
--

INSERT INTO `brands` (`id`, `name`) VALUES
(4, 'Honda'),
(3, 'Mazda'),
(6, 'Mitsubishi'),
(1, 'Nissan'),
(5, 'Subaru'),
(2, 'Toyota');

-- --------------------------------------------------------

--
-- Структура таблицы `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Полное название для отображения',
  `model` varchar(100) NOT NULL,
  `generation` varchar(100) DEFAULT NULL,
  `year_from` int(11) NOT NULL,
  `year_to` int(11) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `engine_type` varchar(100) DEFAULT NULL,
  `engine_volume` decimal(3,1) DEFAULT NULL,
  `engine_power` int(11) DEFAULT NULL,
  `transmission` varchar(50) DEFAULT NULL,
  `drive` varchar(50) DEFAULT NULL,
  `fuel` varchar(50) DEFAULT 'Бензин',
  `color` varchar(50) DEFAULT NULL,
  `mileage` int(11) DEFAULT NULL,
  `condition` enum('Новый','С пробегом','Реставрирован') DEFAULT 'С пробегом',
  `badge_text` varchar(100) DEFAULT NULL,
  `model_code` varchar(50) DEFAULT NULL,
  `grade_info` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `cars`
--

INSERT INTO `cars` (`id`, `brand_id`, `name`, `model`, `generation`, `year_from`, `year_to`, `price`, `engine_type`, `engine_volume`, `engine_power`, `transmission`, `drive`, `fuel`, `color`, `mileage`, `condition`, `badge_text`, `model_code`, `grade_info`, `image_url`, `image_alt`, `description`, `is_available`, `views`, `created_at`) VALUES
(1, 1, 'Nissan Skyline GT-R R32', 'Skyline GT-R', 'R32', 1991, NULL, 6210000, 'RB26DETT', 2.6, 280, '5MT', '4WD', 'Бензин', 'Gun Grey', 68000, 'С пробегом', 'Godzilla', 'BNR32', 'Аукционная оценка 4.5B', 'images/GT-R R32.jpg', 'Nissan Skyline GT-R R32', 'Легендарный Godzilla. Полностью стоковый двигатель, оригинальный кузов без сварки. Аукционная оценка 4.5B. Масло менялось каждые 5000 км. Не дрифтили, не бит. Идеальный кандидат для коллекции или постройки Time Attack.', 1, 0, '2026-04-11 01:46:08'),
(2, 1, 'Nissan Skyline GT-R R34', 'Skyline GT-R', 'R34', 1999, NULL, 12870000, 'RB26DETT', 2.6, 330, '6MT', '4WD', 'Бензин', 'Bayside Blue', 52000, 'С пробегом', 'V-Spec II', 'BNR34', 'Аукционная оценка 5A', 'images/GT-R R34.jpg', 'Nissan Skyline GT-R R34', 'V-Spec II. Один владелец в Японии. Полный сервис у дилера. Задние сиденья не использовались. Компрессия во всех цилиндрах ровная. Турбины оригинал. Выпуск HKS, но родной в комплекте. Без ДТП. Документы по таможне в порядке.', 1, 0, '2026-04-11 01:46:08'),
(3, 2, 'Toyota Supra A80', 'Supra', 'A80', 1997, NULL, 8055000, '2JZ-GTE', 3.0, 330, '6MT', 'RWD', 'Бензин', 'Silver', 52400, 'Реставрирован', '2JZ', 'JZA80', 'Оценка 4B', 'images/Toyota Supra A80.jpg', 'Toyota Supra A80', 'Легенда из Fast & Furious. Полная реставрация кузова в 2022. Двигатель перебран с оригинальными прокладками. Тюнинг: интеркулер GReddy, выхлоп HKS, турбина на сток бусте. Диски BBS LM. Оригинальный велюровый салон. Не трековая, городская.', 1, 0, '2026-04-11 01:46:08'),
(4, 3, 'Mazda RX-7 FD3S', 'RX-7', 'FD3S', 1999, NULL, 4878000, '13B-REW', 1.3, 280, '5MT', 'RWD', 'Бензин', 'Montego Blue', 73200, 'С пробегом', 'Rotary', 'FD3S', 'Оценка 3.5C', 'images/Mazda RX-7.jpg', 'Mazda RX-7 FD3S', 'Роторная душа. Свежий ребилд мотора (маслосъемные колпачки, уплотнения). Турбины оригинал. Кузов обработан от коррозии. Кондиционер работает. Интерьер без заломов. Идеальный кандидат для проекта.', 1, 0, '2026-04-11 01:46:08'),
(5, 4, 'Honda NSX NA1', 'NSX', 'NA1', 1992, NULL, 11520000, 'C30A', 3.0, 280, '5MT', 'RWD', 'Бензин', 'Championship White', 41100, 'Новый', 'NSX', 'NA1', 'Коллекционное состояние', 'images/Honda NSXNA.jpg', 'Honda NSX NA1', 'Классическая Honda NSX. Пробег реальный, подтверждённый. Полностью сервисная история. Titanium exhaust. Электрические сиденья. Нет ДТП. Иммортализирована Сенной. Коллекционный экземпляр.', 1, 0, '2026-04-11 01:46:08'),
(6, 5, 'Subaru Impreza 22B STI', 'Impreza', '22B STI', 1998, NULL, 15300000, 'EJ22', 2.2, 350, '5MT', '4WD', 'Бензин', 'Rally Blue', 39800, 'С пробегом', '22B', 'GC8', 'Лимитированная серия', 'images/Subaru Impreza.jpg', 'Subaru Impreza 22B STI', 'Один из 400. Омологированный раллийный зверь. Полностью оригинал, включая диски и подвеску. Владелец — фанат Subaru. Не участвовал в гонках. Гаражное хранение. Сервис только у официального дилера в Японии.', 1, 0, '2026-04-11 01:46:08'),
(7, 6, 'Mitsubishi Lancer Evolution VI', 'Lancer Evolution', 'VI GSR', 1999, NULL, 5670000, '4G63', 2.0, 280, '5MT', '4WD', 'Бензин', 'Scotia White', 62500, 'С пробегом', 'Evo VI', 'CP9A', 'Tommi Makinen Edition', 'images/Mitsubishi Lancer Evolution.jpg', 'Mitsubishi Lancer Evolution VI', 'Томми Макинен edition. Усиленная подвеска, активный задний дифференциал. Двигатель без капиталки, компрессия в норме. Не чипован. Стоковый интеркулер и турбина. Редкий цвет. Документы чисты.', 1, 0, '2026-04-11 01:46:08'),
(8, 2, 'Toyota AE86 Trueno', 'AE86', 'Trueno', 1986, NULL, 3420000, '4A-GE', 1.6, 130, '5MT', 'RWD', 'Бензин', 'Panda', 94200, 'Реставрирован', 'Hachi-Roku', 'AE86', 'Initial D style', 'images/Toyota AE86 Trueno.jpg', 'Toyota AE86 Trueno', 'Initial D style. Полная реставрация кузова, новая ходовая. Двигатель перебран, карбюраторы Weber. LSD дифференциал. Кузов без гнили. Легендарный Hachi-Roku для дрифта.', 1, 0, '2026-04-11 01:46:08');

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `car_id`, `created_at`) VALUES
(1, 2, 2, '2026-04-15 19:20:42');

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_text` text NOT NULL,
  `full_text` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `title`, `short_text`, `full_text`, `image_url`, `created_at`) VALUES
(1, 'Новое поступление: Nissan Skyline GT-R R34 V-Spec II', 'В наш шоурум прибыл легендарный Skyline R34 в цвете Bayside Blue. Полностью оригинальный, с подтверждённым пробегом 52 000 км.', 'Мы рады сообщить, что в Ярославль прибыл один из самых желанных автомобилей JDM-культуры – Nissan Skyline GT-R R34 V-Spec II. Автомобиль был выкуплен на закрытом аукционе в Токио и прошёл полную предпродажную подготовку. Машина полностью стоковая, за исключением выпускной системы HKS (оригинал в наличии). Компрессия во всех цилиндрах ровная, кузов без следов коррозии. Приглашаем на осмотр в наш шоурум на Дорожной, 22.', 'https://minicarsmoscow.ru/image/cache/catalog/Tovari/MINIGT/14.03.25/gtr/mf_r34_36-1024x683-auto_width_1000.jpg', '2026-04-11 01:46:08'),
(2, 'Открытие нового шоурума в Ярославле', 'С 1 мая 2026 года мы работаем по новому адресу: ул. Дорожная, д. 22. Просторный зал на 15 автомобилей и собственная реставрационная мастерская.', 'Дорогие друзья! Мы рады объявить об открытии нашего нового шоурума в Ярославле. Теперь мы располагаемся на территории бывшего завода «Ярославский моторный» по адресу ул. Дорожная, д. 22. В нашем зале одновременно могут разместиться до 15 легендарных автомобилей. Также заработала собственная мастерская по реставрации и тюнингу. Ждём вас ежедневно с 9:00 до 21:00!', 'https://a.d-cd.net/yYAAAgDqqeA-960.jpg', '2026-04-01 01:46:08'),
(3, 'Сезонный техосмотр для всех клиентов Shift & Drift', 'Только до конца мая – бесплатная диагностика ходовой части и проверка кондиционера для всех автомобилей, приобретённых у нас.', 'Уважаемые клиенты! В преддверии летнего сезона мы запускаем акцию: для всех владельцев автомобилей, купленных в Shift & Drift, мы проведём бесплатную диагностику подвески и системы кондиционирования. Также проверим уровни технических жидкостей и дадим рекомендации по обслуживанию. Акция действует до 31 мая 2026 года. Запись по телефону +7 (4852) 60-88-22.', 'https://static.tildacdn.com/tild3832-6137-4235-b532-393637653132/servicing-hero-1920-.jpg', '2026-04-06 01:46:08');

-- --------------------------------------------------------

--
-- Структура таблицы `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `request_type` enum('test_drive','price','callback','trade_in','auction','logistics','restoration','inspection','tuning','finance') NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('new','in_progress','done','cancelled') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `requests`
--

INSERT INTO `requests` (`id`, `name`, `phone`, `email`, `car_id`, `request_type`, `message`, `status`, `created_at`) VALUES
(1, 'Иван Петров', '+7 999 123 45 67', 'ivan@mail.ru', 2, 'test_drive', 'Хочу записаться на тест-драйв Nissan Skyline R34 в ближайшие выходные.', 'done', '2026-04-11 01:52:22'),
(2, 'Мария Смирнова', '+7 916 555 88 22', 'maria@yandex.ru', NULL, 'auction', 'Интересует подбор Toyota Supra A80 на аукционе. Бюджет до 8 млн руб.', 'new', '2026-04-11 01:52:22'),
(3, 'Алексей Сидоров', '+7 905 777 33 11', 'alex@example.com', 5, 'price', 'Какова окончательная цена на Subaru Impreza 22B с учётом доставки в регион?', 'in_progress', '2026-04-11 01:52:22'),
(4, 'Елена Ким', '+7 962 111 22 33', 'elena@kim.ru', NULL, 'logistics', 'Нужна доставка контейнера из Владивостока в Ярославль.', 'done', '2026-04-11 01:52:22'),
(6, 'Шарова Елизавета Сергеевна', '+79159706575', 'sarovaelizaveta007@gmail.com', NULL, 'test_drive', 'Хочу прокатиться!', 'new', '2026-04-15 19:20:24'),
(7, 'Шарова Елизавета Сергеевна', '+79159706575', 'sarovaelizaveta007@gmail.com', 1, 'test_drive', '', 'new', '2026-04-15 19:27:12');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `car_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `text` text NOT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `client_name`, `car_id`, `rating`, `text`, `is_approved`, `created_at`) VALUES
(1, 'Артем Ш.', 1, 5, 'Взял R32 у ребят. Машина полностью соответствует описанию. Сделали предпродажную подготовку, пригнали из Японии без сюрпризов. Документы идеально. Рекомендую Shift & Drift!', 1, '2026-04-11 01:46:08'),
(2, 'Денис М.', 3, 5, 'Supra A80 — моя мечта. Команда помогла с оформлением, кредитом. Авто просто бомба. Спасибо!', 1, '2026-04-11 01:46:08'),
(3, 'Кирилл П.', 4, 5, 'RX-7. Давно хотел ротор. Ребята подобрали идеальный вариант, дали гарантию на двигатель. Дрифчу на здоровье!', 1, '2026-04-11 01:46:08'),
(4, 'Илья С.', 2, 5, 'R34 V-Spec — это не машина, это искусство. Спасибо за быстрый трейд-ин моего старого авто. Всё честно.', 1, '2026-04-11 01:46:08'),
(5, 'Андрей Л.', 7, 4, 'AE86 немного сюрпризов по электрике, но ребята быстро помогли. В целом отлично. Ездит как зверь.', 1, '2026-04-11 01:46:08');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `full_name`, `password_hash`, `is_admin`, `created_at`) VALUES
(1, 'admin', 'admin@shiftdrift.jp', 'Администратор', 'admin123123', 1, '2026-04-11 01:46:08'),
(2, 'essharova', 'sarovaelizaveta007@gmail.com', 'Шарова Елизавета Сергеевна', 'essharova', 0, '2026-04-15 19:03:54');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Индексы таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`car_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
