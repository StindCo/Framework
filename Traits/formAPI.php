<?php
  namespace StindPattern;
  trait formAPI {
        public function insert_image($nature) {
          if(\is_array($_FILES['images']['name'])) {
            $res = $this->uploads_more($_FILES['images'],'image',$nature);
          } else {
            $res = $this->uploads_one($_FILES['images'],'image',$nature);
          }
          \var_dump($res);
        }
        public function uploads_more($fichiers, $nature, $fonction) {
          foreach($fichiers as $k => $v) {
            for ($i=0; $i < count($v); $i++) {
              $tab[$i][$k] = $v[$i];
            }
          }
          for ($i=0; $i < \count($tab) ; $i++) {
            $res[$i] = $this->uploads_one($tab[$i],$nature,$fonction);
          }
          foreach ($res as $key => $value) {
            if($value == false) {
              $error[$key] = $tab[$key]['name'];
            }
          }
          return array($res,$error);
        }
        public function get_all_extension($t){
          $arra = \explode("/",$t);
          return '.'.$arra[1];
        }
        public function uploads_one($fichier, $nature, $fonction) {
          if(!\is_dir('./media/images/'.$fonction)) {
            \mkdir('./media/images/'.$fonction);
          }
          $pathdir = './media/images/'.$fonction.'/';
          if(\preg_match('/'.$nature.'/i',$fichier['type'])) {
            $uniq = \uniqid($fonction);
            $extension = $this->get_extension($fichier['type']);
            $uploadfile = $pathdir . basename($uniq).$extension;
            if (move_uploaded_file($fichier['tmp_name'], $uploadfile)) {
              return $uploadfile;
            } else {
              return false;
            }
          } else {
            return false;
          }

        }
        public function get_extension($t) {
          if(\preg_match('/jpg/i',$t) ){
            return '.jpg';
          } elseif (preg_match('/png/i',$t)) {
            return '.png';
          } elseif (preg_match('/jpeg/i',$t)) {
            return '.jpeg';
          }elseif (preg_match('/gif/i',$t)) {
            return '.gif';
          }
        }
  }
?>
