function eXcell_cp(cell){
	try{
		this.cell=cell;
		this.grid=this.cell.parentNode.grid;
	}
	catch (er){}
	this.edit=function(){
		this.val=this.getValue()
		this.obj=document.createElement("SPAN");
		this.obj.style.border="1px solid black";
		this.obj.style.position="absolute";
		this.obj.style.zIndex = "9999";
		var arPos = this.grid.getPosition(this.cell); //,this.grid.objBox
		this.colorPanel(4, this.obj)
		document.body.appendChild(this.obj);          //this.grid.objBox.appendChild(this.obj);
		this.obj.style.left=arPos[0]+"px";
		this.obj.style.top=arPos[1]+this.cell.offsetHeight+"px";
	}
	this.toolDNum=function(value){
		if (value.length == 1)
			value='0'+value;
		return value;
	}
	this.colorPanel=function(index, parent){
		var tbl = document.createElement("TABLE");
		parent.appendChild(tbl)
		tbl.cellSpacing=0;
		tbl.editor_obj=this;
		tbl.style.cursor="default";
		tbl.onclick=function(e){
			var ev = e||window.event
			var cell = ev.target||ev.srcElement;
			var ed = cell.parentNode.parentNode.parentNode.editor_obj
			ed.setValue(cell._bg)
			ed.grid.editStop();
		}
		var cnt = 256 / index;
		for (var j = 0; j <= (256 / cnt); j++){
			var r = tbl.insertRow(j);

			for (var i = 0; i <= (256 / cnt); i++){
				for (var n = 0; n <= (256 / cnt); n++){
					R=new Number(cnt*j)-(j == 0 ? 0 : 1)
					G=new Number(cnt*i)-(i == 0 ? 0 : 1)
					B=new Number(cnt*n)-(n == 0 ? 0 : 1)
					var rgb =
						this.toolDNum(R.toString(16))+""+this.toolDNum(G.toString(16))+""+this.toolDNum(B.toString(16));
					var c = r.insertCell(i);
					c.width="10px";
					c.innerHTML="&nbsp;"; //R+":"+G+":"+B;//
					c.title=rgb.toUpperCase()
					c.style.backgroundColor="#"+rgb
					c._bg="#"+rgb;

					if (this.val != null&&"#"+rgb.toUpperCase() == this.val.toUpperCase()){
						c.style.border="2px solid white"
					}
				}
			}
		}
	}
	this.getValue=function(){
		return this.cell.firstChild._bg||""; //this.getBgColor()
	}
	this.getRed=function(){
		return Number(parseInt(this.getValue().substr(1, 2), 16))
	}
	this.getGreen=function(){
		return Number(parseInt(this.getValue().substr(3, 2), 16))
	}
	this.getBlue=function(){
		return Number(parseInt(this.getValue().substr(5, 2), 16))
	}
	this.detach=function(){
		if (this.obj.offsetParent != null)
			document.body.removeChild(this.obj);
		//this.obj.removeNode(true)
		return this.val != this.getValue();
	}
}
eXcell_cp.prototype=new eXcell;
eXcell_cp.prototype.setValue=function(val){
	this.setCValue("<div style='width:100%;height:"+((this.grid.multiLine?cell.offsetHeight-2:16))+";background-color:"+(val||"")
		+";border:0px;'>&nbsp;</div>",
		val);
	this.cell.firstChild._bg=val;
}