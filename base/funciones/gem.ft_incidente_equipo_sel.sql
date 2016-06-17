CREATE OR REPLACE FUNCTION "gem"."ft_incidente_equipo_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		SISTEMA DE GESTION DE MANTENIMIENTO
 FUNCION: 		gem.ft_incidente_equipo_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'gem.tincidente_equipo'
 AUTOR: 		 (admin)
 FECHA:	        08-11-2012 17:11:06
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:	
 AUTOR:			
 FECHA:		
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'gem.ft_incidente_equipo_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'GEM_GEEQIN_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		08-11-2012 17:11:06
	***********************************/

	if(p_transaccion='GEM_GEEQIN_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						geeqin.id_incidente_equipo,
						geeqin.id_uni_cons,
						geeqin.id_falla_evento,
						geeqin.descripcion,
						geeqin.estado_reg,
						geeqin.fecha_desde,
						geeqin.fecha_hasta,
						geeqin.fecha_reg,
						geeqin.id_usuario_reg,
						geeqin.fecha_mod,
						geeqin.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from gem.tincidente_equipo geeqin
						inner join segu.tusuario usu1 on usu1.id_usuario = geeqin.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = geeqin.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'GEM_GEEQIN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		08-11-2012 17:11:06
	***********************************/

	elsif(p_transaccion='GEM_GEEQIN_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_incidente_equipo)
					    from gem.tincidente_equipo geeqin
					    inner join segu.tusuario usu1 on usu1.id_usuario = geeqin.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = geeqin.id_usuario_mod
					    where ';
			
			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
					
	else
					     
		raise exception 'Transaccion inexistente';
					         
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
ALTER FUNCTION "gem"."ft_incidente_equipo_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
