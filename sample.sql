CREATE TABLE `sample` (
  `id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sample` (`id`, `quantity`, `description`) VALUES
(1, 1, 'One'),
(2, 2, 'Two');

ALTER TABLE `sample` ADD PRIMARY KEY (`id`);
ALTER TABLE `sample` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
