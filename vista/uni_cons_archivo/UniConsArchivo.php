<?php
/**
*@package       pXP
*@file          UniConsArchivo.php
*@author        (rac)
*@date          26-10-2012 18:08:27
*@description   Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.UniConsArchivo=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		Phx.vista.UniConsArchivo.superclass.constructor.call(this,config);
		this.init();
		this.load({params:{
			start:0, 
			limit:50,
			id_uni_cons: this.id_uni_cons
			}});
		this.addButton('btnUpload', {
                text : 'Upload archivo',
                iconCls : 'bupload1',
                disabled : true,
                handler : SubirArchivo,
                tooltip : '<b>Upload</b><br/>Subir Archivo'
        });
        
        function SubirArchivo()
        {                   
            var rec=this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_mantenimiento/vista/uni_cons_archivo/SubirArchivo.php',
            'Subir Archivo',
            {
                modal:true,
                width:450,
                height:150
            },rec.data,this.idContenedor,'SubirArchivo')
        }
        
        this.iniciarEventos();
        this.Atributos[2].valorInicial = this.id_uni_cons;        
	},
			
	Atributos:[	       
        {
            config:{                
                name:'tipo',
                fieldLabel:'Tipo',
                allowBlank:false,
                emptyText:'Tipo...',
                store: ['imagen','documento'],
                forceSelection:true,
                triggerAction: 'all',
                mode:'local',
            },
            type:'ComboBox',
            id_grupo:0,
            filters:{   
                pfiltro:'tipo',
                type:'string'
            },
            grid:false,
            form:true
        },
		{
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_uni_cons_archivo'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_uni_cons'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
				name: 'id_uni_cons_archivo_padre',
				fieldLabel: 'id_uni_cons_archivo_padre',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100,
				maxLength:4
			},
			type:'NumberField',
			filters:{pfiltro:'unidoc.id_uni_cons_archivo_padre',type:'numeric'},
			id_grupo:1,
			grid:false,
			form:false
		},
		{
            config:{
                name: 'nombre',
                fieldLabel: 'Nombre',
                allowBlank: false,
                anchor: '100%',
                gwidth: 100,
                maxLength:30
            },
            type:'TextField',
            filters:{pfiltro:'unidoc.nombre',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                fieldLabel: "Link",
                gwidth: 130,
                inputType:'file',
                name: 'archivo',
                buttonText: '',   
                maxLength:150,
                anchor:'100%',
                renderer:function (value, p, record){  
                            if(record.data['extension'].length!=0)
                            return  String.format('{0}',"<div style='text-align:center'><a href = '../../../archivos_uni_cons/"+ record.data['archivo']+"' align='center' width='70' height='70'>documento</a></div>");
                        },  
                buttonCfg: {
                    iconCls: 'upload-icon'
                }
            },
            type:'Field',
            sortable:false,
            id_grupo:0,
            grid:true,
            form:false
        },
        {
            config:{
                name: 'codigo',
                fieldLabel: 'Codigo',
                allowBlank: true,
                anchor: '100%',
                gwidth: 100,
                maxLength:5
            },
            type:'TextField',
            filters:{pfiltro:'unidoc.codigo',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'nombre_archivo',
                fieldLabel: 'Nombre Archivo',
                allowBlank: false,
                anchor: '100%',
                gwidth: 100,
                maxLength:30
            },
            type:'TextField',
            filters:{pfiltro:'unidoc.nombre_archivo',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },        
		{
			config:{
				name: 'extension',
				fieldLabel: 'Extension',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100,
				maxLength:5
			},
			type:'TextField',
			filters:{pfiltro:'unidoc.extension',type:'string'},
			id_grupo:1,
			grid:true,
			form:false
		},
		{
            config:{                
                name:'reporte',
                fieldLabel:'Reporte',
                allowBlank:false,
                emptyText:'Reporte?...',
                store: ['si','no'],
                forceSelection:true,
                triggerAction: 'all',
                mode:'local',
            },
            type:'ComboBox',
            id_grupo:0,
            filters:{   
                pfiltro:'reporte',
                type:'string'
            },
            grid:true,
            form:true
        },
		{
			config:{
				name: 'resumen',
				fieldLabel: 'Resumen',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100,
				maxLength:100
			},
			type:'TextField',
			filters:{pfiltro:'unidoc.resumen',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'palabras_clave',
				fieldLabel: 'Palabras Clave',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100,
				maxLength:20
			},
			type:'TextField',
			filters:{pfiltro:'unidoc.palabras_clave',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100,
				maxLength:10
			},
			type:'TextField',
			filters:{pfiltro:'unidoc.estado_reg',type:'string'},
			id_grupo:1,
			grid:false,
			form:false
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				//gwidth: 100,
				//renderer:function (value,p,record){return value?value.dateFormat('d/m/Y h:i:s'):''},
				format : 'Y-m-d',
				hidden:true
			},
			type:'DateField',
			filters:{pfiltro:'unidoc.fecha_reg',type:'date'},
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
				gwidth: 109,
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
				//gwidth: 109,
				//renderer:function (value,p,record){return value?value.dateFormat('d/m/Y h:i:s'):''}
				format:'Y-m-d',
				hidden:true
			},
			type:'DateField',
			filters:{pfiltro:'unidoc.fecha_mod',type:'date'},
			id_grupo:1,
			grid:true,
			form:false
		}
	],
	title:'Archivos de Unidades Constructoras',
	ActSave:'../../sis_mantenimiento/control/UniConsArchivo/insertarUniConsArchivo',
	ActDel:'../../sis_mantenimiento/control/UniConsArchivo/eliminarUniConsArchivo',
	ActList:'../../sis_mantenimiento/control/UniConsArchivo/listarUniConsArchivo',
	id_store:'id_uni_cons_archivo',
	fields: [
		{name:'id_uni_cons_archivo', type: 'numeric'},
		{name:'id_uni_cons_archivo_padre', type: 'numeric'},
		{name:'extension', type: 'string'},
		{name:'reporte', type: 'string'},
		{name:'resumen', type: 'string'},
		{name:'palabras_clave', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'codigo', type: 'string'},
		{name:'nombre', type: 'string'},
		{name:'archivo', type: 'string'},
		{name:'nombre_archivo', type: 'string'},
		{name:'fecha_reg', type: 'timestamp'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'timestamp'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'id_uni_cons', type: 'numeric'}
	],
	sortInfo:{
		field: 'id_uni_cons_archivo',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
	fwidht: 450,
	fheight: 300,
	preparaMenu:function(tb){
        Phx.vista.UniConsArchivo.superclass.preparaMenu.call(this,tb)
        this.getBoton('btnUpload').enable();
    },
    
    liberaMenu:function(tb){
        Phx.vista.UniConsArchivo.superclass.liberaMenu.call(this,tb)
        this.getBoton('btnUpload').disable();      
    },
    
    iniciarEventos:function()
    {       
        this.ocultarComponente(this.getComponente('reporte'));
        this.getComponente('tipo').on('select',function(c,r,n){
                if(n=='imagen' || n=='0'){
                    this.getComponente('reporte').enable();
                    this.mostrarComponente(this.getComponente('reporte'));
                }else{
                    this.ocultarComponente(this.getComponente('reporte'));
                    this.getComponente('reporte').setValue('no');
                    this.getComponente('reporte').disable();
                }
                
        },this);
    },
    
    onButtonEdit:function(){
        datos=this.sm.getSelected().data;
        Phx.vista.UniConsArchivo.superclass.onButtonEdit.call(this); //sobrecarga enable select
        if(datos.reporte=='si'){
            this.getComponente('reporte').enable();
            this.mostrarComponente(this.getComponente('reporte'));
        }else{
            this.getComponente('reporte').setValue('no');
            this.getComponente('reporte').disable();
            this.ocultarComponente(this.getComponente('reporte'));
        }
    },
    
    south:{
          url:'../../../sis_mantenimiento/vista/uni_cons_archivo/ListarVersionesArchivo.php',
          title:'Versiones de archivo',
          height:'40%', 
          width:400,
          cls:'ListarVersionesArchivo'
    },
    codReporte:'S/C',
	codSistema:'GEM',
	pdfOrientacion:'L'
})
</script>
		
		