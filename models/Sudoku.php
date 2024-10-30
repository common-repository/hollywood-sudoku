<?php

class hs_Hollywood_Sudoku implements Iterator
{
	public $origin;
	public $state;//EX: ......52..8.4......3...9...5.1...6..2..7........3.....6...1..........7.4.......3.
	public $data; //Array[row][col]

	//Iterator Indices
	private $row;
	private $col;

	public function __construct()
	{
		return $this->import(".................................................................................");
	}

	public function import($string=false)
	{
		if(strlen(trim($string))!=81)return $this;
		$this->data = array();
		for($row=0;$row<9;$row++)
		{
			$this->data[$row] = array();
			for($col=0;$col<9;$col++)
			{
				$i = $row*9+$col;
				$this->data[$row][$col] = is_numeric($string[$i]) ? (int) $string[$i]
					: 0
					;
			}
		}
		$this->origin = $this->toString();
		return $this;
	}

	public function random(){
		//load a puzzle from the file
		$fname = dirname(__FILE__)."/puzzles.data";
		$puzzles = FILE($fname);
		$end = count($puzzles);
		if($end)
		{
			$puzzle = $puzzles[(int)mt_rand(0,$end-1)];
			return $this->import($puzzle);
		}
		return $this;
	}

	public function toString(){return $this->save()->state;}

	public function toDebug()
	{
		$out ="";
		$line = function($msg)use(&$out){$out .= "<br>$msg";};
		$line($this->origin);

		for($r=0;$r<9;$r++){
			$tmp = "";
			foreach($this->getRow($r) as $val)$tmp .= $val." ";
			$line($tmp);
		}
		return "<code>$out</code>";
	}


	public function apply($string)
	{
		$setter = new hs_Hollywood_Sudoku();
		$setter->import($string);
		foreach($setter as $rowCol => $val)
			if($val)$this->set($rowCol[0],$rowCol[1],$val);
		return $this;
	}


	///////////////////////////////////////////////////////////////////////////////////
	// Protected

/***************************************************************************

Hint: address lookup of (b,p)
    (0,0)   (0,1)   (0,2)   (1,0)   (1,1)   (1,2)   (2,0)   (2,1)   (2,2)
    (0,3)   (0,4)   (0,5)   (1,3)   (1,4)   (1,5)   (2,3)   (2,4)   (2,5)
    (0,6)   (0,7)   (0,8)    ...     ...     ...     ...     ...     ...
    (3,0)   (3,1)   (3,2)    ...     ...     ...     ...     ...     ...
    (3,3)   (3,4)   (3,5)    ...     ...     ...     ...     ...     ...
    (3,6)   (3,7)   (3,8)    ...     ...     ...     ...     ...     ...
    (6,0)   (6,1)   (6,2)   (7,0)   (7,1)   (7,2)   (8,0)   (8,1)   (8,2)
    (6,3)   (6,4)   (6,5)    ...     ...     ...     ...     ...     ...
    (6,6)   (6,7)   (6,8)    ...     ...     ...     ...     ...     ...




Hint: address lookup of (r,c)
    (0,0)   (0,1)   (0,2)   (0,3)   (0,4)   (0,5)   (0,6)   (0,7)   (0,8)
    (1,0)    ...     ...     ...     ...     ...     ...     ...     ...
    (2,0)    ...     ...     ...     ...     ...     ...     ...     ...
    (3,0)    ...     ...     ...     ...     ...     ...     ...     ...
    (4,0)    ...     ...     ...     ...     ...     ...     ...     ...
    (5,0)    ...     ...     ...     ...     ...     ...     ...     ...
    (6,0)    ...     ...     ...     ...     ...     ...     ...     ...
    (7,0)    ...     ...     ...     ...     ...     ...     ...     ...
    (8,0)    ...     ...     ...     ...     ...     ...     ...     ...

**************************************************************************/

	protected function getRow($row)
	{
		return $this->data[$row];
	}

	protected function getCol($col)
	{
		$out = array();
		for($i=0;$i<9;$i++)$out[]=$this->data[$i][$col];
		return $out;
	}

	protected function getBox($box){
		$out = array();
		$rowStart = 3*(int)($box/3);
		$rowEnd   = $rowStart+3;
		$colStart = 3*($box%3);
		$colEnd   = $colStart+3;
		for($r=$rowStart;$r<$rowEnd;$r++)for($c=$colStart;$c<$colEnd;$c++){
			$out[] = $this->data[$r][$c];
		}
		return $out;
	}

	protected function getBoxFromRowCol($row,$col){ return (($col-($col%3))/3) + ((($row-($row%3))/3)*3); }
	protected function getPosFromRowCol($row,$col){ return ($col % 3) + (($row % 3) * 3); }
	protected function getRowFromBoxPos($box,$pos){	return 3*(int)(($box - ($box%3))/3) + (int)($pos/3); }
	protected function getColFromBoxPos($box,$pos){	return 3*((int)$box%3)+($pos%3); }

	protected function bp2rc($bp){
		$r = $this->getRowFromBoxPos($bp[0],$bp[1]);
		$c = $this->getColFromBoxPos($bp[0],$bp[1]);
		return $r.$c;
	}

	protected function rc2bp($rc){
		$b = $this->getBoxFromRowCol($rc[0],$rc[1]);
		$p = $this->getPosFromRowCol($rc[0],$rc[1]);
		return $b.$p;
	}

	protected function clamp($min,$max,$val)
	{
		settype($min,"integer");
		settype($max,"integer");
		settype($val,"integer");
		return max($min,min($max,$val));
	}

	protected function set($row,$col,$val)
	{
		$row = $this->clamp(0,8,$row);
		$col = $this->clamp(0,8,$col);
		$val = $this->clamp(0,9,$val);

		$this->data[$row][$col]=$val; 

		return $this;
	}

	protected function setBP($box,$pos,$val){
		$r = $this->getRowFromBoxPos($box,$pos);
		$c = $this->getColFromBoxPos($box,$pos);
		return $this->set($r,$c,$val);
	}


	///////////////////////////////////////////////////////////////////////////////////
	// Private
	private function save()
	{
		$this->state = "";

		foreach($this as $rowCol => $value){
			$this->state .= $value ? $value : ".";		
		}

		return $this;
	}


	///////////////////////////////////////////////////////////////////////////////////
	// Iterator Interface
	public function rewind(){$this->row=0;$this->col=0;}
	public function current(){return $this->data[$this->row][$this->col];}
	public function key(){return $this->row.$this->col;}
	public function valid(){return $this->row<9 && $this->col<9;}
	public function next(){
		$this->col += 1;
		if(! ($this->col<9)){
			$this->row++;
			$this->col=0;
		}
	}


}
