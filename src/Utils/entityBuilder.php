<?php
	namespace StindPattern\Utils;

	class EntityBuilder extends  RepositoryBuilder{

    protected function creator (String $classe, $category=null) {
			$classToLoad = \ucfirst($classe);
				$namespaceClass = "Entities\\".$classToLoad."Entity";
				$file = DMODELS.'Entities/'.$classe.'.php';
			if (\is_file($file)) {
				if(!class_exists("abstractEntity")) {
					include CONFIGS.'utils/abstractEntity.php';
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
