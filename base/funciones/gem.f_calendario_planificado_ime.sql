CREATE OR REPLACE FUNCTION gem.f_calendario_planificado_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		SISTEMA DE GESTION DE MANTENIMIENTO
 FUNCION: 		gem.f_calendario_planificado_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'gem.tcalendario_planificado'
 AUTOR: 		 (admin)
 FECHA:	        02-11-2012 15:11:40
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:	
 AUTOR:			
 FECHA:		
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_calendario_planificado	integer;
    
    v_fecha_ini  date;
    v_dif integer;
    v_id_uni_cons_mant_predef integer;
    v_estado varchar;
    v_tipo varchar;
    v_aux varchar;

BEGIN

    v_nombre_funcion = 'gem.f_calendario_planificado_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'GEM_CALE_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		02-11-2012 15:11:40
	***********************************/

	if(p_transaccion='GEM_CALE_INS')then

        begin
        	--Sentencia de la insercion
        	insert into gem.tcalendario_planificado(
			estado_reg,
			estado,
			tipo,
			fecha_fin,
			observaciones,
			fecha_ini,
			id_usuario_reg,
			fecha_reg,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.estado,
			v_parametros.tipo,
			v_parametros.fecha_fin,
			v_parametros.observaciones,
			v_parametros.fecha_ini,
			p_id_usuario,
			now(),
			null,
			null
			)RETURNING id_calendario_planificado into v_id_calendario_planificado;
               
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Calendario almacenado(a) con exito (id_calendario_planificado'||v_id_calendario_planificado||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_calendario_planificado',v_id_calendario_planificado::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'GEM_CALE_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		02-11-2012 15:11:40
	***********************************/

	elsif(p_transaccion='GEM_CALE_MOD')then

		begin
			--Sentencia de la modificacion
			update gem.tcalendario_planificado set
			estado = v_parametros.estado,
			tipo = v_parametros.tipo,
			fecha_fin = v_parametros.fecha_fin,
			observaciones = v_parametros.observaciones,
			fecha_ini = v_parametros.fecha_ini,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now()
			where id_calendario_planificado=v_parametros.id_calendario_planificado;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Calendario modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_calendario_planificado',v_parametros.id_calendario_planificado::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'GEM_CALE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		02-11-2012 15:11:40
	***********************************/

	elsif(p_transaccion='GEM_CALE_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from gem.tcalendario_planificado
            where id_calendario_planificado=v_parametros.id_calendario_planificado;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Calendario eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_calendario_planificado',v_parametros.id_calendario_planificado::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;
     
  	/*********************************    
 	#TRANSACCION:  'GEM_OBTCALPLA_EMI'
 	#DESCRIPCION:	Obtiene datos del calendario planificado indicado
 	#AUTOR:		rac 	
 	#FECHA:		02-11-2012 15:11:40
	***********************************/

	elsif(p_transaccion='GEM_OBTCALPLA_EMI')then

		begin
			--Sentencia de la eliminacion
			select c.fecha_ini into 
                   v_fecha_ini
            from gem.tcalendario_planificado c
            where id_calendario_planificado=v_parametros.calendario_planificado;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Calendario eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_calendario_planificado',v_parametros.calendario_planificado::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'fecha_ini',v_fecha_ini::varchar);
           
              
            --Devuelve la respuesta
            return v_resp;

		end;  
        
        
        
        
        
    /*********************************    
 	#TRANSACCION:  'GEM_UPDCALPLA_IME'
 	#DESCRIPCION:	Modifica el calendario planificado
 	#AUTOR:		rac	
 	#FECHA:		02-11-2012 15:11:40
	***********************************/

	elsif(p_transaccion='GEM_UPDCALPLA_IME')then

		begin
        
            --Llamada a función para actualizar el calendario
            v_aux = gem.f_actualizar_calendario(p_id_usuario,v_parametros.id_calendario_planificado,v_parametros.fecha_ini,v_parametros.recursivo);
            
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Calendario modificado desde vista de calendario(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_calendario_planificado',v_parametros.id_calendario_planificado::varchar);
            
            --Devuelve la respuesta
            return v_resp;
            
		end;
        
        
	else
     
    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

EXCEPTION

	WHEN OTHERS THEN
		v_resp='';
		v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
		v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
		v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
		raise exception '%',v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;