<?php

/**
 * Created by REES46
 * User: Michael Kechinov
 * Date: 15/06/2017
 * Time: 16:00
 * Сегментатор хранит сегмент в $_SESSION
 * Может быть несколько активных сегментов
 * Сегментатор возвращает NULL вместо сегмента для поисковых ботов и пустых user-agent.
 */
class ServerSideSegmentator {

	/**
	 * Префикс имени в сессии
	 */
	const PREFIX = 'r46_segment_';

	/**
	 * Текущий сегмент
	 * @var string
	 */
	private $segment;

	/**
	 * @param string $name  Имя сесси
	 * @param int    $count Количество сегментов
	 * @throws Exception
	 */
	public function __construct($name, $count) {
		$segments = range('A', 'Z');

		if( $count < 1 || $count > count($segments) ) {
			throw new Exception('Incorrect segments count');
		}

		// Если сессия еще не запущена, запускаем
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}

		// Пропускаем ботов
		if( !$this->isBot() ) {

			// Генерируем сегмент, если его не было ранее
			if( empty($_SESSION[self::PREFIX . $name]) || !in_array($_SESSION[self::PREFIX . $name], $segments) ) {
				$this->segment = $segments[rand(1, $count) - 1];
				$_SESSION[self::PREFIX . $name] = $this->segment;
			} else {
				$this->segment = $_SESSION[self::PREFIX . $name];
			}
		}
	}

	/**
	 * @return string|null
	 */
	public function getSegment() {
		return $this->segment;
	}

	/**
	 * Проверяет ботов
	 * @return bool
	 */
	private function isBot() {
		return empty($_SERVER['HTTP_USER_AGENT']) || preg_match('/YandexMetrika|Googlebot|bingbot|Screenshot Bot|YandexAntivirus|PEBOT|LWP::Simple|BBBike|wget|curl|msnbot|scrapbot|BLEXBot/', $_SERVER['HTTP_USER_AGENT']);
	}
}