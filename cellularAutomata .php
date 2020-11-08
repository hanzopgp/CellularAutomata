<?php

class WorldState{

    private $cells = array();
    private $age;

    /**
     * Constructeur
     */
    public function __construct(int $nbCells = 100){
        $this->age = 0;
        for($i=0;$i<$nbCells;$i++){
            array_push($this->cells, false);
        }
    }

    /**
     * Getter de l'attribut $cells
     */
    public function getCells(){
        return $this->cells;
    }

    /**
     * Getter de l'attribut $age
     */
    public function getAge(){
        return $this->age;
    }

    /**
     * Setter de l'attribut $cells
     */
    public function setCells($tabCells){
        $this->cells = $tabCells;
    }

    /**
     * Construit une instance de WorldState et rend vivante 5 cellules au hasard et renvoie cette instance
     */
    public static function buildRandomWorld(int $nbCells, float $ratioAlive){
        $world = new WorldState($nbCells);
        $ratio = $nbCells * $ratioAlive;

        //ON CREE LES INDEX QUI SERONT A TRUE
        $randoms = array();
        for($i=0;$i<$ratio;$i++){
            array_push($randoms, rand(0,$nbCells));
        }

        //SI $I EST CONTENU DANS UN DES INDEX, ALORS $cell[$i] = true
        for($i=0;$i<$nbCells;$i++){
            if(in_array($i, $randoms)){
                $world->cells[$i] = true;
            }
        }

        return $world;
    }

    /**
     * Check si une cellule à une position donnée est "vivante" (true) et renvoie true si c'est le cas,
     * false si elle est "morte", ou lève une exception si cette cellule n'existe pas.
     */
    public function isCellAliveAtPosition(int $index){

        //monde en cercle
        if($index == -1){
          $index = sizeOf($this->cells) - 1;
        }
        if($index == sizeOf($this->cells)){
          $index = 0;
        }

        if(isset($this->cells[$index])){
            if($this->cells[$index]){
                return true;
            }else{
                return false;
            }
        }else{
            throw new Exception("La cellule que vous demandez n'existe pas !");
        }
    }

	public function computeNextGeneration(EvolutionRule $evolutionRule){
    $this->age++; 
		$tmp = array();
		for($i = 0; $i < sizeOf($this->cells); $i++){
			array_push($tmp, $evolutionRule->computeNextStateCell($this->isCellAliveAtPosition($i - 1), $this->isCellAliveAtPosition($i), $this->isCellAliveAtPosition($i + 1)));
		}
    $worldNext = WorldState::setCells($tmp);
		return $worldNext;
	}

}






class Simulation{

  private $worldTmp;
  private $evolutionRule;

  public function __construct(WorldState $worldTmp = null, EvolutionRule $evolutionRule){
      $this->worldTmp = $worldTmp;
      $this->evolutionRule = $evolutionRule;
  }

  public function displayEvolution($nbGenerations, Displayer $displayer){
    for($i = 0; $i < $nbGenerations; $i++){
      //sleep(1);
      ($this->worldTmp)->computeNextGeneration($this->evolutionRule);
      $string = $displayer->displayWorld($this->worldTmp);
      echo $string . "\n";
    }
  }

}






interface EvolutionRule{

	public function computeNextStateCell($leftAlive, $selfAlive, $rightAlive);

}






// class Rule110 implements EvolutionRule{

// 	public function computeNextStateCell($leftAlive, $selfAlive, $rightAlive){
// 		$res = true;
// 		if($leftAlive && $selfAlive && $rightAlive){
// 			$res = false;
// 		}
// 		if($leftAlive && !$selfAlive && !$rightAlive){
// 			$res = false;
// 		}
// 		if(!$leftAlive && !$selfAlive && !$rightAlive){
// 			$res = false;
// 		}
// 		return $res;
// 	}
 
// }




// class Rule126 implements EvolutionRule{

//   public function computeNextStateCell($leftAlive, $selfAlive, $rightAlive){
//     $res = true;
//     if($leftAlive && $selfAlive && $rightAlive){
//       $res = false;
//     }
//     if(!$leftAlive && !$selfAlive && !$rightAlive){
//       $res = false;
//     }
//     return $res;
//   }
 
// }






// class Rule184 implements EvolutionRule{

//   public function computeNextStateCell($leftAlive, $selfAlive, $rightAlive){
//     $res = false;
//     if($leftAlive && $selfAlive && $rightAlive){
//       $res = true;
//     }
//     if($leftAlive && !$selfAlive && $rightAlive){
//       $res = true;
//     }
//     if($leftAlive && !$selfAlive && !$rightAlive){
//       $res = true;
//     }
//     if(!$leftAlive && $selfAlive && $rightAlive){
//       $res = true;
//     }
//     return $res;
//   }
 
// }







class AllRules implements EvolutionRule{

  public $nbRule;

  public function __construct($nbRule){
    $this->nbRule = $nbRule;
  }

  public function computeNextStateCell($leftAlive, $selfAlive, $rightAlive){
  
    $n = $this->nbRule;
    $n = decbin($n);
    $n = str_split($n);
    $n2 = array();
    foreach($n as $e){
      $tmp = $e == "1" ? true : false;
      array_push($n2, $tmp);
    }

    $index = 8 - sizeOf($n2);
    for($i = 0; $i < $index; $i++){
      array_unshift($n2, false);
    }

    $res = false;
    if($leftAlive && $selfAlive && $rightAlive){
      $res = $n2[0];
    }
    if($leftAlive && $selfAlive && !$rightAlive){
      $res = $n2[1];
    }
    if($leftAlive && !$selfAlive && $rightAlive){
      $res = $n2[2];
    }
    if($leftAlive && !$selfAlive && !$rightAlive){
      $res = $n2[3];
    }
    if(!$leftAlive && $selfAlive && $rightAlive){
      $res = $n2[4];
    }
    if(!$leftAlive && $selfAlive && !$rightAlive){
      $res = $n2[5];
    }
    if(!$leftAlive && !$selfAlive && $rightAlive){
      $res = $n2[6];
    }
    if(!$leftAlive && !$selfAlive && !$rightAlive){
      $res = $n2[7];
    }
    return $res;
  }
 
}









interface Displayer{

  public function displayWorld(WorldState $world);

}







abstract class TerminalDisplayer implements Displayer{

  public function displayWorld(WorldState $world){
    $string = "";
    foreach($world->getCells() as $cell){
      $cell ? $string.= "#" : $string.="-";
    }
    $this->iterationControl();
    return $string;
  }

  public function iterationControl(){}

}





class InteractiveTerminalDisplayer extends TerminalDisplayer{

  public function iterationControl(){
    readline();
  }
  
}




class PausingTerminalDisplayer extends TerminalDisplayer{

  public function iterationControl(){
    usleep(50000);
  }

}




class StatsDisplayer implements Displayer{

  public function displayWorld(WorldState $world){
    $ratioAlive = $this->countRatioAlive($world);
    $string = "Age : " . strval($world->getAge()) . ", RatioAlive : " . strval($ratioAlive) . "\n";
    return $string;
  }

  public function countRatioAlive($world){
    usleep(50000);
    $countALive = 0;
    $countDead = 0;
    foreach($world->getCells() as $cells){
      if($cells == true){
        $countALive++;
      }
      else{
        $countDead++;
      }   
    }
    return $countALive/$countDead;
  }

}






class HtmlDisplayer implements Displayer{

  public function __construct(){
    echo "<pre>";
  }

  public function displayWorld(WorldState $world){
    $string = "";
    foreach($world->getCells() as $cell){
      $cell ? $string.= "#" : $string.="-";
    }
    $this->iterationControl();
    return $string;
  }

  public function iterationControl(){}

}








$nbRule = 101;
if(php_sapi_name() == "cli"){ //SI DANS TERMINAL
  if(sizeOf($argv) != 5){
    $world = WorldState::buildRandomWorld(50, 0.5);
  }
  else{
    $world = WorldState::buildRandomWorld((int)$argv[2], (float)$argv[1]);
    $nbRule = $argv[4];
  }

  // $rule110 = new Rule110();
  // $rule184 = new Rule184();
  // $rule126 = new Rule126();
  $allRules = new AllRules($nbRule);
  $Simulation = new Simulation($world, $allRules);

  $InteractiveTerminalDisplayer = new InteractiveTerminalDisplayer();
  $PausingTerminalDisplayer = new PausingTerminalDisplayer();
  $StatsDisplayer = new StatsDisplayer();

  if(sizeOf($argv) != 5){
    $Simulation->displayEvolution(30, $PausingTerminalDisplayer);
  }
  else{
    $Simulation->displayEvolution((int)$argv[3], $PausingTerminalDisplayer);
  }  
}
else{ //SI SUR LE WEB
  // https://dev-21510242.users.info.unicaen.fr/TW3/TP5/cellularAutomata.php?argument1=0.5&argument2=250&argument3=50&argument4=rule184
  if(sizeOf($_GET)  == 4){
    $argument1 = $_GET['argument1'];
    $argument2 = $_GET['argument2'];
    $argument3 = $_GET['argument3'];
    $argument4 = $_GET['argument4'];
  }

  if(sizeOf($_GET) != 4){
    $world = WorldState::buildRandomWorld(50, 0.5);
  }
  else{
    $world = WorldState::buildRandomWorld((int)$argument2, (float)$argument1);
  }

  $rule = new AllRules(110);
  if(isset($argument4)){
    // if($argument4 == "rule110"){
    //   $rule = new Rule110();
    // }
    // if($argument4 == "rule184"){
    //   $rule = new Rule184();
    // }
    // if($argument4 == "rule126"){
    //   $rule = new Rule126();
    // }
    $rule = new AllRules($argument4);
  }
  
  $Simulation = new Simulation($world, $rule);

  $InteractiveTerminalDisplayer = new InteractiveTerminalDisplayer();
  $PausingTerminalDisplayer = new PausingTerminalDisplayer();
  $StatsDisplayer = new StatsDisplayer();
  $HtmlDisplayer = new HtmlDisplayer();

  if(sizeOf($_GET) != 4){
    $Simulation->displayEvolution(30, $HtmlDisplayer);
  }
  else{
    $Simulation->displayEvolution((int)$argument3, $HtmlDisplayer);
  }
}



?>
