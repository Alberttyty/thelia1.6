<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) 2005-2013 OpenStudio                                           */
/*      email : info@thelia.fr                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program.  If not, see <http://www.gnu.org/licenses/>.        */
/*                                                                                   */
/*************************************************************************************/
require_once __DIR__ . "/../fonctions/autoload.php";
/*
* Outil de cache à deux niveaux :
* - utilisant les éléments static de PHP, permettant de s'assurer qu'une requete n'est pas exécutée 2 fois sur une meme page
* - utilisant MEMCACHE
*
* Configuration du niveau : attribut LEVEL
* - LEVEL=self::AUCUN_CACHE     : pas de cache
* - LEVEL=self::STATIC_CACHE    : cache uniquement en memoire
* - LEVEL=selff::MEMCACHE_CACHE : cache en memoire et via memcached
*
* fonctionne comme un Singleton :
* $cache=CacheBase::getCache();
*
* puis :
* $cache->set("cle","valeur");
* $valeur=$cache->get("cle");
*
* Pour mettre en cache le résultat d'un SELECT :
* $cache->query("requete")
*
* Pour mettre en cache le résultat d'un COUNT SQL :
* $cache->query_count("requete")
*
* Pour changer temporairement le LEVEL
* $cache->switchLevel(x)  -> la prochaine opération (query...) se fera avec ce level temporaire
*
*/
class CacheBase
{
		// Durée de vie d'un item dans le cache memcache (en secondes)
		const AGE = 600;

		const AUCUN_CACHE = 0;
		const STATIC_CACHE = 1;
		const MEMCACHE_CACHE = 2;

		public static $LEVEL = 2;

		private $levelhistory;
		private $result_cache = [];
		private $memcache = false;

		// singleton
		private static $cache = null;

		private $cnx;

		private function __construct()
		{
				$this->cnx = new Cnx();

				// Déterminer le niveau courant en fonction
				// de la valeur de la variable mecached
				// 0 => cache local, <> 0 => utiliser memcache
				self::$LEVEL = $this->is_memcache_active() ? self::MEMCACHE_CACHE : self::STATIC_CACHE;

				$this->levelhistory = self::$LEVEL;
		}

		public static function getCache()
		{
				if (!self::$cache) self::$cache = new CacheBase();
				return self::$cache;
		}

		public function switchLevel($level)
		{
				$this->levelhistory = self::$LEVEL;
				self::$LEVEL = $level;
		}

		private function is_memcache_active()
		{
				// On ne peut pas utiliser Variable::lire() ici, qui fait appel à
				// cete classe.

	      //si on est dans le processus de maj on empêche l'activation de memcache
	      if (defined('IN_UPDATE_THELIA')) return false;

				$val = 1;

				$hdl = $this->cnx->query("select valeur from ".Variable::TABLE." where nom = 'memcache'");

				if ($hdl) $val = $this->cnx->get_result($hdl);

				return intval($val) > 0;
		}

		private function switchBackLevel()
		{
				self::$LEVEL = $this->levelhistory;
		}

		private function getMemcache()
		{
				if (self::$LEVEL != self::MEMCACHE_CACHE) return null;

				if ($this->memcache === false) {
						$this->memcache = new Memcache();

						if (! $this->memcache->connect('localhost')) {
								Tlog::fatal("Echec de connexion au serveur memcache. Arrêt.");
								die("Echec de connexion au serveur memcache. Arrêt.");
						}
				}

				return $this->memcache;
		}

		private function setCache2($key, $value)
		{
				if (self::$LEVEL != self::MEMCACHE_CACHE) return false;
				$this->getMemcache()->set($key, $value, false, self::AGE);
		}

		private function getCache2($key)
		{
				if (self::$LEVEL != self::MEMCACHE_CACHE) return false;
				return $this->getMemcache()->get($key);
		}

		private function flushCache2()
		{
				if (self::$LEVEL != self::MEMCACHE_CACHE) return false;
				return $this->getMemcache()->flush();
		}

		public function get($key)
		{
				if (self::$LEVEL == 0) return false;

				$hash = hash('md5', $key);

				$retour = !isset($this->result_cache[$hash]) ? false : $this->result_cache[$hash];

				if (!$retour) { // ce n'est pas dans le niveau 1
						$retour = $this->getCache2($hash);

						if ($retour == false) return false; // ce n'est pas dans le niveau 2
						else $this->result_cache[$hash] = $retour; // On le met en niveau 1 pour accélérer le prochain get
				}
				return $retour;
		}

		public function set($key, $value)
		{
				if (self::$LEVEL == 0) return;

				$hash = hash('md5', $key);
				$this->result_cache[$hash] = $value;
				$this->setCache2($hash, $value);
		}

		public function query($query, $clazz = false)
		{
				$data = $this->get($query);

				if (! $data) {
						$data = [];

						$resul = $this->cnx->query($query);

						while ($resul && $row = $this->fetch_object($resul, $clazz)) {
								$data[] = $row;
						}

						$this->set($query, $data);
				}

				$this->switchBackLevel();

				return $data;
		}

		public function fetch_object($resul, $clazz = false)
		{
				return $this->cnx->fetch_object($resul, $clazz);
		}

		public function query_count($query)
		{
				$num = $this->get($query);

				if ($num < 0 || $num == "") {
						$resul = $this->cnx->query($query);

						if ($resul) $num = $this->cnx->num_rows($resul);
						else $num = 0;

						$this->set($query, $num);
				}

				$this->switchBackLevel();

				return $num;
		}

		public function reset_cache()
		{
				$this->result_cache = [];
				$this->flushCache2();
		}

		/* Compatibilité pré-1.5.2 */

		public function mysql_query($query, $link = null, $clazz = false)
		{
				return $this->query($query, $clazz);
		}

		public function mysql_fetch_object($resul, $clazz = false)
		{
				return $this->fetch_object($resul, $clazz);
		}

		public function mysql_query_count($query, $link = false)
		{
				return $this->query_count($query);
		}

}

?>
