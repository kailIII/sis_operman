CREATE OR REPLACE FUNCTION "gem"."ft_licencia_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Sistema de Mantenimiento Industrial
 FUNCION: 		gem.ft_licencia_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'gem.tlicencia'
 AUTOR: 		 (admin)
 FECHA:	        17-04-2017 03:18:41
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

	v_nombre_funcion = 'gem.ft_licencia_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'GM_GEMLIC_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		17-04-2017 03:18:41
	***********************************/

	if(p_transaccion='GM_GEMLIC_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						gemlic.id_licencia,
						gemlic.calificacion_curso,
						gemlic.id_conductor,
						gemlic.nro_licencia,
						gemlic.tipo,
						gemlic.fecha_exp,
						gemlic.fecha_autoriz,
						gemlic.estado_reg,
						gemlic.fecha_curso,
						gemlic.id_usuario_ai,
						gemlic.fecha_reg,
						gemlic.usuario_ai,
						gemlic.id_usuario_reg,
						gemlic.fecha_mod,
						gemlic.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from gem.tlicencia gemlic
						inner join segu.tusuario usu1 on usu1.id_usuario = gemlic.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = gemlic.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'GM_GEMLIC_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		17-04-2017 03:18:41
	***********************************/

	elsif(p_transaccion='GM_GEMLIC_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_licencia)
					    from gem.tlicencia gemlic
					    inner join segu.tusuario usu1 on usu1.id_usuario = gemlic.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = gemlic.id_usuario_mod
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
ALTER FUNCTION "gem"."ft_licencia_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
