CREATE OR REPLACE FUNCTION "gem"."ft_analisis_mant_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		SISTEMA DE GESTION DE MANTENIMIENTO
 FUNCION: 		gem.ft_analisis_mant_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'gem.tanalisis_mant'
 AUTOR: 		 (admin)
 FECHA:	        30-09-2012 21:44:06
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
	v_id_analisis_mant	integer;
			    
BEGIN

    v_nombre_funcion = 'gem.ft_analisis_mant_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'GEM_GEANMA_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		30-09-2012 21:44:06
	***********************************/

	if(p_transaccion='GEM_GEANMA_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into gem.tanalisis_mant(
			id_uni_cons,
			id_tipo_mant,
			id_persona_rev,
			estado_reg,
			fecha_emision,
			descripcion,
			fecha_rev,
			id_usuario_reg,
			fecha_reg,
			id_usuario_mod,
			fecha_mod,
			id_persona_prep,
			id_uni_cons_hijo,
			id_uo
          	) values(
			v_parametros.id_uni_cons,
			v_parametros.id_tipo_mant,
			v_parametros.id_persona_rev,
			'activo',
			v_parametros.fecha_emision,
			v_parametros.descripcion,
			v_parametros.fecha_rev,
			p_id_usuario,
			now(),
			null,
			null,
			v_parametros.id_persona_prep,
			v_parametros.id_uni_cons_hijo,
			v_parametros.id_uo
			)RETURNING id_analisis_mant into v_id_analisis_mant;
               
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Análisis de Mantenimiento almacenado(a) con exito (id_analisis_mant'||v_id_analisis_mant||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_analisis_mant',v_id_analisis_mant::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'GEM_GEANMA_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		30-09-2012 21:44:06
	***********************************/

	elsif(p_transaccion='GEM_GEANMA_MOD')then

		begin
			--Sentencia de la modificacion
			update gem.tanalisis_mant set
			id_uni_cons = v_parametros.id_uni_cons,
			id_tipo_mant = v_parametros.id_tipo_mant,
			id_persona_rev = v_parametros.id_persona_rev,
			fecha_emision = v_parametros.fecha_emision,
			descripcion = v_parametros.descripcion,
			fecha_rev = v_parametros.fecha_rev,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_persona_prep = v_parametros.id_persona_prep,
			id_uni_cons_hijo = v_parametros.id_uni_cons_hijo,
			id_uo = v_parametros.id_uo
			where id_analisis_mant=v_parametros.id_analisis_mant;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Análisis de Mantenimiento modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_analisis_mant',v_parametros.id_analisis_mant::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'GEM_GEANMA_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		30-09-2012 21:44:06
	***********************************/

	elsif(p_transaccion='GEM_GEANMA_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from gem.tanalisis_mant
            where id_analisis_mant=v_parametros.id_analisis_mant;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Análisis de Mantenimiento eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_analisis_mant',v_parametros.id_analisis_mant::varchar);
              
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
$BODY$
LANGUAGE 'plpgsql' VOLATILE
COST 100;
ALTER FUNCTION "gem"."ft_analisis_mant_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
