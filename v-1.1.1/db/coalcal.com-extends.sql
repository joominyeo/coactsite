-- -----------------------------------------------------------------------------------------
-- start of v-1.0.0 
-- ------------------------------------------------------------------------------------------

--
-- Table structure for table `school`
--

CREATE TABLE IF NOT EXISTS `school` (
`id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `school`
--
ALTER TABLE `school`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `school`
--
ALTER TABLE `school`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- user table add fields
ALTER TABLE `user` ADD `school_id` INT NOT NULL DEFAULT '0' ;
ALTER TABLE `user` ADD `grade` ENUM('9','10','11','12') NOT NULL DEFAULT '9' ;

-- add more fields to school table
ALTER TABLE `school` ADD `created` DATETIME NOT NULL;
ALTER TABLE `school` ADD `last_updated` DATETIME NOT NULL ;
ALTER TABLE `school` ADD `disabled` BOOLEAN NOT NULL DEFAULT FALSE ;
ALTER TABLE `school` ADD `deleted` BOOLEAN NOT NULL DEFAULT FALSE ;
ALTER TABLE `school` ADD `logo_id` INT NOT NULL DEFAULT '0' COMMENT 'pictures.id refrence' AFTER `name`;


-- 

--
-- Table structure for table `pictures`
--

CREATE TABLE IF NOT EXISTS `pictures` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `width` smallint(6) NOT NULL,
  `height` smallint(6) NOT NULL,
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Bytes',
  `orig_fname` varchar(100) NOT NULL,
  `stored_fname` varchar(100) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `violation_of_terms` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pictures`
--
ALTER TABLE `pictures`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pictures`
--
ALTER TABLE `pictures`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;



-- -----------------------------------------------------------------------------------------
-- end of v-1.0.0 and start of v-1.1.0
-- ------------------------------------------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE IF NOT EXISTS `event` (
`id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `apply_deadline` datetime DEFAULT NULL,
  `type` enum('internships','job shadows','volunteering') NOT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event`
--
ALTER TABLE `event`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


-- -----------------------------------------------


--
-- Table structure for table `event_notification`
--

CREATE TABLE IF NOT EXISTS `event_notification` (
`id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event_notification`
--
ALTER TABLE `event_notification`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event_notification`
--
ALTER TABLE `event_notification`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---------------------------------------

CREATE TABLE IF NOT EXISTS `user_event_notification` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Indexes for table `user_event_notification`
--
ALTER TABLE `user_event_notification`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_event_notification`
--
ALTER TABLE `user_event_notification`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---------------------------------------

CREATE TABLE IF NOT EXISTS `user_event` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Indexes for table `user_event`
--
ALTER TABLE `user_event`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_event`
--
ALTER TABLE `user_event`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- -----------------------------------------------------------------------------------------
-- end of v-1.1.0 and start of v-1.1.1 
-- ------------------------------------------------------------------------------------------
