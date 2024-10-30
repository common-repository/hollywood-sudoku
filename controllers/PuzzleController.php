<?php

class hs_Hollywood_SudokuSolver extends hs_Hollywood_Sudoku
{

	public function __construct()
	{
		set_time_limit(10);
		parent::__construct();
		$this->possible = array();
		$this->goodHint = true;
		foreach(array(1,2,3,4,5,6,7,8,9)as$digit)$this->possible[$digit]=new hs_Hollywood_Sudoku();
	}


	public function test()
	{
		$out ="";
		$line = function($msg)use(&$out){$out .= "<br>$msg";};

		$out .= "<h2>Board:</h2>";
		$out .= $this->toDebug();

		foreach(array(0,1,4,6,8) as $box){

			$out .= "<h2>Box $box:</h2>";
			$tmp = "";
			foreach($this->getBox($box) as $i => $val){
				$tmp .= $val." ";
				if(! (($i+1)%3)){
					$line($tmp);
					$tmp="";
				}
			}
		}

		$out .= "<h2>RC to BP</h2>";
		$line("(0,0) = ".$this->rc2bp("00"));
		$line("(2,2) = ".$this->rc2bp("22"));
		$line("(4,0) = ".$this->rc2bp("40"));
		$line("(4,4) = ".$this->rc2bp("44"));
		$line("(4,8) = ".$this->rc2bp("48"));
		$line("(8,8) = ".$this->rc2bp("88"));
		
		$out .= "<h2>BP 2 RC</h2>";
		$line("(0,0) = ".$this->bp2rc("00"));
		$line("(0,8) = ".$this->bp2rc("08"));
		$line("(3,3) = ".$this->bp2rc("33"));
		$line("(4,4) = ".$this->bp2rc("44"));
		$line("(5,5) = ".$this->bp2rc("55"));
		$line("(8,8) = ".$this->bp2rc("88"));

		return "<code>$out</code>";
	}


	public function hint()
	{
		$hint = new hs_Hollywood_Sudoku();

		$digits = array(1,2,3,4,5,6,7,8,9);
		foreach($digits as $digit){
			$this->markPossible($digit);
			$bp = $this->hunt($digit);
			if(strlen($bp)){
				$hint->setBP($bp[0],$bp[1],$digit);
				$this->goodHint = true;
				return $hint->toString();
			}
		}
		$this->goodHint = false;
		return $hint->toString();
	}

	protected function hunt($digit,$strategy=0)
	{
		$bp = ""; //Returns the BoxPos of the place for this digit

		//Recursively dig for a place to put this digit
		//Each strategy applies a slightly different bit of logic

		foreach(array(0,1,2,3,4,5,6,7,8) as $box){
			switch($strategy){
				case 2:
					//COL STRATEGY
					if($box<3){
						foreach(array(0,1,2)as$pos){
							$col = $this->getColFromBoxPos($box,$pos);
							$places = 0;
							$possibleRow = -1;
							foreach($this->possible[$digit]->getCol($col) as $row => $val){
								if($val==2){
									$places++;
									$possibleRow = $row;
								}
							}
							if($places==1){ //Only one place for this digit in this colummn
								$bp = $this->rc2bp($possibleRow.$col);
								break; //foreach column from this box
							}
						}
					}
					break;
				case 1:
					//ROW STRATEGY
					if($box%3==0){
						foreach(array(0,3,6)as$pos){
							$row = $this->getRowFromBoxPos($box,$pos);
							$places = 0;
							$possibleCol = -1;
							foreach($this->possible[$digit]->getRow($row) as $col => $val){
								if($val==2){
									$places++;
									$possibleCol = $col;
								}
							}
							if($places==1){ //Only one place for this digit in this row
								$bp = $this->rc2bp($row.$possibleCol);
								break; //foreach column from this box
							}
						}
					}
					break;
				case 0:
					//BOX STRATEGY
					$rc = $this->bp2rc($box."0");
					$x = $rc[0];
					$y = $rc[1];
					$s = array();
					$t = array();
					$possibles = 0;
					$changes = 0;
					
					$clear = function($r,$c)use(&$changes,$digit){
									if($this->possible[$digit]->data[$r][$c]==2){
										$changes++;
										$this->possible[$digit]->data[$r][$c] = 0;
									}
								};


					for($r=$x,$i=0;$i<3;$r++,$i++)for($c=$y,$j=0;$j<3;$c++,$j++){
						if($this->possible[$digit]->data[$r][$c]==2){
							$possibles++;
							$s[$possibles]=$r;
							$t[$possibles]=$c;
						}
					}
					if($possibles==3){
						//BOX TRIPLES: all box possibles in same row/col then clear possible in other box's row/col
        				if($s[1]==$s[2]&&$s[2]==$s[3]){
            				//In same Row - remove possibles from other zones in same Row
            				for($c=0;$c<9;$c++){
                				$clear($s[1],$c);
            				}
        				}
        				if($t[1]==$t[2]&&$t[2]==$t[3]){
            				//In same Col - remove possibles from other zones in same Col
            				for($r=0;$r<9;$r++){
                				$clear($r,$t[1]);
            				}
        				}
        				//Put Back the possibles cuz they were removed
        				$this->possible[$digit]->data[$s[1]][$t[1]]=2;
        				$this->possible[$digit]->data[$s[2]][$t[2]]=2;
        				$this->possible[$digit]->data[$s[3]][$t[3]]=2;
					}elseif($possibles==2){
						//BOX DOUBLES
        				if($s[1]==$s[2])
            				for($c=0;$c<9;$c++)
                				$clear($s[1],$c);
        				if($t[1]==$t[2])
            				for($r=0;$r<9;$r++)
                				$clear($r,$t[1]);
        				//Put Back the possibles cuz they were removed
        				$this->possible[$digit]->data[$s[1]][$t[1]]=2;
        				$this->possible[$digit]->data[$s[2]][$t[2]]=2;
					}elseif($possibles==1){
						//BOX SINGLES
						//Take It!
						$bp = $this->rc2bp($s[1].$t[1]);
					}
					if(strlen($bp)==0 && $changes>$possibles)
						$bp = $this->hunt($digit,$strategy); //repeat this strategy because we cleared some possibles
					break;
			}
			if(strlen($bp))return $bp;
		}
		//Recurse and try next strategy
		if($strategy<2)$bp = $this->hunt($digit,++$strategy);
		return $bp;
	}


	protected function markPossible($digit)
	{
		$rowContainsDigit = function ($row,$digit){return in_array($digit,$this->getRow($row));};
		$colContainsDigit = function ($col,$digit){return in_array($digit,$this->getCol($col));};
		$boxContainsDigit = function ($box,$digit){return in_array($digit,$this->getBox($box));};

		$this->possible[$digit] = new hs_Hollywood_Sudoku();

		//Scan Row for Digit, if not found increment Possible
		for($row=0;$row<9;$row++)
			if(!$rowContainsDigit($row,$digit))
				for($col=0;$col<9;$col++)
					if(!$this->data[$row][$col])
						$this->possible[$digit]->data[$row][$col] += 1;

		//Scan col for Digit, if not found increment Possible
		for($col=0;$col<9;$col++)
			if(!$colContainsDigit($col,$digit))
				for($row=0;$row<9;$row++)
					if(!$this->data[$row][$col])
						$this->possible[$digit]->data[$row][$col] += 1;

		//check each box and clear possibles if digit in box
		foreach(array(0,1,2,3,4,5,6,7,8) as $box)
			if($boxContainsDigit($box,$digit))
				foreach(array(0,1,2,3,4,5,6,7,8) as $pos)
					$this->possible[$digit]->setBP($box,$pos,0);

    	//By now each possible location should have a 2 in it.
    	//This indicates that the row and col are clear at this location
    	//and thus is a possible location for this digit...

	}

	public function incomplete()
	{
		return strpos($this->toString(),".")!==false;
	}
	
	public function progressing(){return $this->goodHint;}


}
