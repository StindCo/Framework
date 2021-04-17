<?php
	namespace StindPattern\Utils;

	class RepositoryBuilder extends Builder {
		private $category = "Utils";
		private $params = NULL;
		public function __construct($name=null) {
			if(!\is_null($name)) {
				$this->name = $name;
				return $this->get_instance();
			}
		}
		public function set_name(String $name) {
			$this->name = $name;
			return $this;
		}
		public function set_category(String $category) {
			$this->category = $category;
			return $this;
		}
		public function set_params ($params) {
			$this->params = $params;
			return $this;
		}
		protected function creator (String $classe, $category=null) {
			$classToLoad = \ucfirst($classe);
				$namespaceClass = "Repositories\\".$classToLoad."Repository";
				$file = DMODELS.'Repositories/'.$classe.'.php';
			if (\is_file($file)) {
				if(!class_exists("abstractRepository")) {
					  include CONFIGS.'utils/abstractRepository.php';
				}
				require $file;
				return new $namespaceClass($this->params);
			} else {
				return false;
			}
		}
		public function get_instance() {
			return $this->creator($this->name);
		}
	}

?>
