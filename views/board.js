
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

cell_size = 30;
function Cell(r,c,val){
	this.val = val == '.' ? '' : val;
	this.r = r;
	this.c = c;
	this.b = ((r - (r % 3)) / 3) + (((c - (c % 3)) / 3)*3);
	this.p = (r % 3) + ((c % 3) * 3);
	this.isWrong = false;
	this.isHint = false;
	this.paint();
}

Cell.prototype.paint = function(){
	function clear(cell){
		board.ctx.clearRect(cell.r*cell_size,cell.c*cell_size,cell_size,cell_size);
	}

	function draw(cell){
		var borderColor = "#3f3b38";
		var focusColor = "#1f1cdd";
		var textColor = "#3f3c3d";
		var hintColor = "#7ff87d";
		var wrongColor = "#f85c5d";

		clear(cell);
		if(cell.isHint)board.ctx.strokeStyle = hintColor;
		else if(cell.isWrong)board.ctx.strokeStyle = wrongColor;
		else board.ctx.strokeStyle = board.canvas===document.activeElement&&(cell.r==board.focusR && cell.c==board.focusC) ? focusColor : borderColor;
		board.ctx.strokeRect(cell.r*cell_size+2,cell.c*cell_size+2,cell_size-4,cell_size-4);
		if(cell.val){
			//TODO: get color for HINT
			var fontH = cell_size/2;
			board.ctx.fillStyle = textColor;
			board.ctx.font = fontH+"px Arial";
			board.ctx.textAlign = "center";
			board.ctx.fillText(cell.val,cell.r*cell_size+(cell_size/2),cell.c*cell_size+(cell_size/2)+(fontH/2)-2);
		}
	}

	draw(this);
}

Cell.prototype.Set = function(val){
	this.val = val == '.' ? '' : val;
	this.paint();
}

/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
board = {};
board.debug = false;
board.focusR = -4;
board.focusC = -4;
board.mobile = function() {
	var check = false;
	(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
	return check;
}

board.paint = function(){
	var i,j,row,col;
	if(!board.ctx){
		var boardHTML = board.mobile() == true  ? '<canvas id="sudoku_canvas" style="outline:none">ERROR: Your browser does not support HTML5 Canvas.</canvas>'
			: '<canvas id="sudoku_canvas" contentEditable=true style="outline:none">ERROR: Your browser does not support HTML5 Canvas.</canvas>';
		jQuery("#sudoku_board").html(boardHTML);
		board.canvas = document.getElementById("sudoku_canvas");
		if(!board.mobile()){
			board.focusR = 4;
			board.focusC = 4;
			board.canvas.focus();
			board.canvas.addEventListener("click",function(e){return board.click(e);},false);
			board.canvas.addEventListener("keydown",function(e){return board.key(e);},false);
			board.canvas.addEventListener("blur",function(e){return board.paint();},false);
			board.canvas.addEventListener("focus",function(e){return board.paint();},false);
		}
		board.ctx = board.canvas.getContext('2d');
		board.canvas.width = 9*cell_size;
		board.canvas.height = 9*cell_size;
	}

	if(!board.cells){
		board.cells = new Array(9);
		for(i=0;i<9;i++)board.cells[i] = new Array(9);

		for(i=0, row=0; row<9;row++)for(col=0;col<9;col++,i++){
			board.cells[row][col]=new Cell(row,col,board.state.charAt(i));
		}

	}else{
		for(i=0, row=0; row<9;row++)for(col=0;col<9;col++,i++){
			board.cells[row][col].isHint = (board.hint.charAt(i)!=".");
			board.cells[row][col].Set(board.state.charAt(i));
		}
	}

	//Paint Grids
	var borderColor = "#5f5b58";
	board.ctx.strokeStyle=borderColor;
	for(row=0;row<3;row++)for(col=0;col<3;col++){
		board.ctx.strokeRect(3*row*cell_size,3*col*cell_size,cell_size*3,cell_size*3);
	}


	//Clear the hint
	board.hint  = ".................................................................................";
}

board.key = function(event){
	var e = event;
	if( !e ) {
		if( window.event ) {
			e = window.event;
		} else {
			return;
		}
	}

	if( typeof( e.which ) == 'number' ) {
		e = e.which;
	} else {
		if( typeof( e.keyCode ) == 'number'  ) {
			e = e.keyCode;
		} else {
			if( typeof( e.charCode ) == 'number'  ) {
				e = e.charCode;
			} else {
				return;
			}
		}
	}

	//We only care about some key pressess...
	switch (e) {
	case 37:    //left
		board.focusR = Math.max(board.focusR-1,0);
		event.preventDefault();
		break;
	case 38:    //up
		board.focusC = Math.max(board.focusC-1,0);
		event.preventDefault();
		break;
	case 39:    //right
		board.focusR = Math.min(board.focusR+1,8);
		event.preventDefault();
		break;
	case 40:    //down
		board.focusC = Math.min(board.focusC+1,8);
		event.preventDefault();
		break;
	case 8:    //backspace
	case 32:   //space
	case 46:   //delete
	case 48:   //0 = clear
	case 96:   //0 = clear
		event.preventDefault();
		board.cells[board.focusR][board.focusC].Set('.');
		break;
	case 49:
	case 50:
	case 51:
	case 52:
	case 53:
	case 54:
	case 55:
	case 56:
	case 57:
		e-=48;
		event.preventDefault();
		board.cells[board.focusR][board.focusC].Set(e);
		break;
	case 97:
	case 98:
	case 99:
	case 100:
	case 101:
	case 102:
	case 103:
	case 104:
	case 105:
		e-=96;
		event.preventDefault();
		board.cells[board.focusR][board.focusC].Set(e);
		break;
	default:
		if(board.debug)console.log("key pressed = "+e);
	}

	board.update();
	board.error();
	board.paint();
	return false;
}

board.click = function(e){
	var x=0;
	var y=0;
	var rect = board.canvas.getBoundingClientRect();

	x = e.clientX - rect.left;
	y = e.clientY - rect.top;
	//Set focus
	board.focusR = Math.floor(x/cell_size);
	board.focusC = Math.floor(y/cell_size);
	board.paint();
}

board.apply = function(data){
	var i,r,c,s,h,next;
	board.hint = data;
	s = board.state.split('');
	h = board.hint.split('');
	next = "";
	board.nohint = true; //Assume no hint (used to trigger a guess)
	for(i=0;i<board.state.length;i++){
		if(h[i] == '.' || s[i] != '.'){
			next += s[i]; //keep existing state
		}else{
			next += h[i]; //Use Hint
			board.nohint = false; //we have a hint
		}
	}
	board.state = next;
	if(board.debug&&board.nohint)console.log("NO HINT");
	board.paint();
}

board.countBox = function(b,val){
	var i,j,row,col,startRow,startCol;
	var next = function(p){
		row = Math.floor(p%3)+startRow;
		col = Math.floor(p/3)+startCol;
		return board.cells[row][col];
	};

	startRow = Math.floor(b%3)*3;
	startCol = Math.floor(b/3)*3;

	j=0; //count
	for(i=0; i<9;i++){
		if(next(i).val == val)j++;
	}

	return j;
}

board.error = function(){
	var i,j,row,col,err;
	err = false;
	//Clear all Errors
	for(i=0, row=0; row<9;row++)for(col=0;col<9;col++,i++)board.cells[row][col].isWrong = false;
	//Find Errors
	for(i=0, row=0; row<9;row++)for(col=0;col<9;col++,i++){
		if(board.cells[row][col].val){
			//Check Column
			for(j=col+1; j<9;j++)if(board.cells[row][col].val == board.cells[row][j].val){board.cells[row][col].isWrong = board.cells[row][j].isWrong = err = true;}
			//check Rows
			for(j=row+1; j<9;j++)if(board.cells[row][col].val == board.cells[j][col].val){board.cells[row][col].isWrong = board.cells[j][col].isWrong = err = true;}
			//Check Box
			if(board.countBox(board.cells[row][col].b,board.cells[row][col].val)>1)board.cells[row][col].isWrong = err = true;
		}
	}

	return err;
}

board.solved = function(){
	var i,j,row,col;
	if(board.error())return true;
	for(i=0, row=0; row<9;row++)for(col=0;col<9;col++,i++){
		if(!board.cells[row][col].val)return false;
	}
	board.hint = board.state;
	return true;
}

board.update = function(){
	var i,j,row,col;
	//Read state from Cells
	board.state = "";
	for(i=0, row=0; row<9;row++)for(col=0;col<9;col++,i++){
		board.state = board.state + ( board.cells[row][col].val ? board.cells[row][col].val : "." ) ;
	}

}

function NewSudoku(){
	cell_size = Math.min((jQuery("#sudoku_board").width()-20)/9,50);
	board.state = ".................................................................................";
	board.hint  = ".................................................................................";
	if(!board.mobile()){
		board.focusR = 4;
		board.focusC = 4;
	}
	board.paint();
	if(!board.mobile()){
		board.canvas.focus();
	}
	jQuery.ajax({url: h_sudoku.new_board }).done(function(data){
		board.state=data.state;
		board.paint();
	});
}

function Hint(){
	jQuery.ajax({url: h_sudoku.hint+board.state}).done(function(data){
		board.apply(data.state);
		if(!board.mobile()){
			board.canvas.focus();
		}
	});
}

function Solve(){
	jQuery.ajax({url: h_sudoku.solve+board.state}).done(function(data){
		board.apply(data.state);
	});
}

function Hollywood(){
	jQuery.ajax({url: h_sudoku.hint+board.state}).done(function(data){
		board.apply(data.state);
		if(!board.solved()
			&& data != "................................................................................."
		)Hollywood();
		else board.paint();
	});
}

/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
function hollywood_sudoku_begin(){if(typeof jQuery == 'function')
	NewSudoku();
	else
		window.setTimeout(hollywood_sudoku_begin,50);
};

window.onload = function(){window.setTimeout(hollywood_sudoku_begin,500);};


