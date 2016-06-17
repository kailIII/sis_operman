<?php
/**
*@package pXP
*@file gen-DiagramaDecision.php
*@author  (admin)
*@date 02-10-2012 01:25:12
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.DiagramaDecision=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.DiagramaDecision.superclass.constructor.call(this,config);
		this.init();
		this.load({params:{start:0, limit:50}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_diagrama_decision'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
				name: 'id_metodologia',
				fieldLabel: 'Metodología',
				allowBlank: false,
				emptyText:'Elija una metodología...',
				store:new Ext.data.JsonStore({
					url: '../../sis_mantenimiento/control/Metodologia/listarMetodologia',
					id: 'id_metodologia',
					root:'datos',
					sortInfo:{
						field:'nombre',
						direction:'ASC'
					},
					totalProperty:'total',
					fields: ['id_metodologia','nombre','codigo'],
					// turn on remote sorting
					remoteSort: true,
					baseParams:{par_filtro:'nombre'}
				}),
				valueField: 'id_metodologia',
				displayField: 'nombre',
				gdisplayField:'desc_metodologia',
				//hiddenName: 'id_administrador',
				forceSelection:true,
				typeAhead: false,
    			triggerAction: 'all',
    			lazyRender:true,
				mode:'remote',
				pageSize:20,
				queryDelay:500,
				anchor: '100%',
				gwidth:220,
				minChars:2,
				renderer:function (value, p, record){return String.format('{0}', record.data['desc_metodologia']);}
			},
			type:'ComboBox',
			filters:{pfiltro:'metodo.nombre',type:'string'},
			id_grupo:0,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'codigo',
				fieldLabel: 'Código',
				allowBlank: false,
				anchor: '100%',
				gwidth: 100,
				maxLength:20
			},
			type:'TextField',
			filters:{pfiltro:'gedide.codigo',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'nombre',
				fieldLabel: 'Nombre',
				allowBlank: false,
				anchor: '100%',
				gwidth: 100,
				maxLength:100
			},
			type:'TextField',
			filters:{pfiltro:'gedide.nombre',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'fecha_desde_validez',
				fieldLabel: 'Válido desde',
				allowBlank: true,
				gwidth: 100,
				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y h:i:s'):''},
				format:'d/m/Y'
			},
			type:'DateField',
			filters:{pfiltro:'gedide.fecha_desde_validez',type:'date'},
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
			filters:{pfiltro:'gedide.estado_reg',type:'string'},
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
			filters:{pfiltro:'gedide.fecha_reg',type:'date'},
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
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y h:i:s'):''},
				hidden:true
			},
			type:'DateField',
			filters:{pfiltro:'gedide.fecha_mod',type:'date'},
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
		}
	],
	title:'Diagrama de Decisión',
	ActSave:'../../sis_mantenimiento/control/DiagramaDecision/insertarDiagramaDecision',
	ActDel:'../../sis_mantenimiento/control/DiagramaDecision/eliminarDiagramaDecision',
	ActList:'../../sis_mantenimiento/control/DiagramaDecision/listarDiagramaDecision',
	id_store:'id_diagrama_decision',
	fields: [
		{name:'id_diagrama_decision', type: 'numeric'},
		{name:'id_metodologia', type: 'numeric'},
		{name:'codigo', type: 'string'},
		{name:'nombre', type: 'string'},
		{name:'fecha_desde_validez', type: 'date', dateFormat:'Y-m-d H:i:s'},
		{name:'estado_reg', type: 'string'},
		{name:'fecha_reg', type: 'date', dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_mod', type: 'date', dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'desc_metodologia', type: 'string'}
		
	],
	sortInfo:{
		field: 'id_diagrama_decision',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
	fwidth: 450,
	fheight: 270,
	south:{
		  url:'../../../sis_mantenimiento/vista/diagrama_decision_accion/DiagramaDecisionAccion.php',
		  title:'Detalle', 
		  height:'50%',	//altura de la ventana hijo
		  //width:'50%',		//ancho de la ventana hjo
		  cls:'DiagramaDecisionAccion'
	},
    codReporte:'S/C',
	codSistema:'GEM',
	pdfOrientacion:'L'
})
</script>
		
		