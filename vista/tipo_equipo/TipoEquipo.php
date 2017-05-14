<?php
/**
*@package pXP
*@file gen-TipoEquipo.php
*@author  (rac)
*@date 08-08-2012 23:50:13
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.TipoEquipo=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.TipoEquipo.superclass.constructor.call(this,config);
		
		this.init();
		this.load({params:{start:0, limit:50}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_tipo_equipo'
			},
			type:'Field',
			form:true 
		},{
			config:{
				name: 'codigo',
				fieldLabel: 'Código',
				allowBlank: false,
				width: '100%',
				gwidth: 100,
				maxLength:20
			},
			type:'TextField',
			filters:{pfiltro:'teq.codigo',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},{
			config:{
				name: 'nombre',
				fieldLabel: 'Nombre',
				allowBlank: false,
				width: '100%',
				gwidth: 250,
				maxLength:200
			},
			type:'TextField',
			filters:{pfiltro:'teq.nombre',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'descripcion',
				fieldLabel: 'Descripción',
				allowBlank: true,
				width: '100%',
				gwidth: 100,
				maxLength:300
			},
			type:'TextArea',
			filters:{pfiltro:'teq.descripcion',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},
		
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
			type:'TextField',
			filters:{pfiltro:'teq.estado_reg',type:'string'},
			id_grupo:1,
			grid:true,
			form:false
		},
		
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4,
				hidden:true
			},
			type:'NumberField',
			filters:{pfiltro:'usu1.cuenta',type:'string'},
			id_grupo:1,
			grid:true,
			form:false
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				
				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y h:i:s'):''},
				hidden:true
			},
			type:'DateField',
			filters:{pfiltro:'teq.fecha_reg',type:'date'},
			id_grupo:1,
			grid:true,
			form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4,
				hidden:true
			},
			type:'NumberField',
			filters:{pfiltro:'usu2.cuenta',type:'string'},
			id_grupo:1,
			grid:true,
			form:false
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y h:i:s'):''},
				hidden:true
			},
			type:'DateField',
			filters:{pfiltro:'teq.fecha_mod',type:'date'},
			id_grupo:1,
			grid:true,
			form:false
		}
	],
	title:'Definir Tipo de Equipos',
	ActSave:'../../sis_mantenimiento/control/TipoEquipo/insertarTipoEquipo',
	ActDel:'../../sis_mantenimiento/control/TipoEquipo/eliminarTipoEquipo',
	ActList:'../../sis_mantenimiento/control/TipoEquipo/listarTipoEquipo',
	id_store:'id_tipo_equipo',
	fields: [
		{name:'id_tipo_equipo', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'nombre', type: 'string'},
		{name:'descripcion', type: 'string'},
		{name:'codigo', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		
		
		{name:'fecha_reg', type: 'date', dateFormat:'Y-m-d H:i:s.u'},
		
		
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date', dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_tipo_equipo',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
	fwidth: 400,
	fheight: 250,
	tabsouth:[{
	  url:'../../../sis_mantenimiento/vista/tipo_variable/TipoVariable.php',
	  title:'Variables Tipo', 
	  height:'50%',
	   cls:'TipoVariable'
	 }, {
		url:'../../../sis_mantenimiento/vista/falla_evento/FallaEvento.php',
		title:'Fallas/Eventos Conocidas', 
		//width:'50%',
		height:'50%',
		cls:'FallaEvento'
	 }, {
	 	url:'../../../sis_mantenimiento/vista/tipo_equipo_col/TipoEquipoCol.php',
	  	title:'Columna Reportes', 
	  	//width:'50%',
	  	height:'50%',
	  	cls:'TipoEquipoCol'
	 }
	],
	codReporte:'S/C',
	codSistema:'GEM',
	pdfOrientacion:'L',
	title1:'REGISTRO',
	title2:'Tipos de Equipos'
})
</script>
		
		