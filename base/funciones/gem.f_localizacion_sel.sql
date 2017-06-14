CREATE OR REPLACE FUNCTION gem.f_localizacion_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		SISTEMA DE GESTION DE MANTENIMIENTO
 FUNCION: 		gem.f_localizacion_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'gem.tlocalizacion'
 AUTOR: 		 (rac)
 FECHA:	        14-06-2012 03:48:41
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
    v_where 			varchar;
    v_join 				varchar;
    v_id_loc			integer;
    v_ids				varchar;
			    
BEGIN

	v_nombre_funcion = 'gem.f_localizacion_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'GEM_loc_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		rac	
 	#FECHA:		14-06-2012 03:48:41
	***********************************/

	if(p_transaccion='GEM_loc_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						loc.id_localizacion,
						loc.codigo,
						loc.estado_reg,
						loc.nombre,
						loc.id_localizacion_fk,
						loc.ubicacion,
						loc.coordenadas,
						loc.desc_ubicacion,
						loc.id_usuario_reg,
						loc.fecha_reg,
						loc.id_usuario_mod,
						loc.fecha_mod,
                        loc.latitud,
                        loc.longitud,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						loc.tipo_numeracion
						from gem.tlocalizacion loc
						inner join segu.tusuario usu1 on usu1.id_usuario = loc.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = loc.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;
        
      /*********************************    
 	#TRANSACCION:  'GEM_LOC_ARB_SEL'
 	#DESCRIPCION:	Consulta de datos en arbol para localizaciones
 	#AUTOR:		rac	
 	#FECHA:		29-08-2011 09:19:28
	***********************************/

	elseif(p_transaccion='GEM_LOC_ARB_SEL')then
     				
    	begin
        
              if(v_parametros.id_padre = '%') then
                v_where := ' loc.id_localizacion_fk is NULL';
                 v_join:= 'LEFT';      
                      
              else
                v_where := ' loc.id_localizacion_fk = '||v_parametros.id_padre;
                v_join := 'INNER';
              end if;
        
        
    		--Sentencia de la consulta
			v_consulta:='select
						loc.id_localizacion,
						loc.codigo,
						loc.estado_reg,
						loc.nombre,
						loc.id_localizacion_fk,
						loc.ubicacion,
						loc.coordenadas,
						loc.desc_ubicacion,
						loc.id_usuario_reg,
						loc.fecha_reg,
						loc.id_usuario_mod,
						loc.fecha_mod,
                        loc.latitud,
                        loc.longitud,
						usu1.cuenta as usr_reg,
						''[''||loc.codigo||''] ''||loc.nombre as texto,
                        case
                          when (loc.id_localizacion_fk is null )then
                               ''raiz''::varchar
                          ELSE
                              ''hijo''::varchar
                          END as tipo_nodo,
                        loc.tipo_numeracion
                        from gem.tlocalizacion loc
						inner join segu.tusuario usu1 
                        on usu1.id_usuario = loc.id_usuario_reg
                        where   loc.estado_reg = ''activo'' and '||v_where; 
                        
                 if pxp.f_existe_parametro(p_tabla,'vista') then
                 	--Obtiene el ID de la localizacion de VEHICULOS
                 	select id_localizacion
                 	into v_id_loc
                 	from gem.tlocalizacion
                 	where codigo = 'VEH';
                 	if v_id_loc is not null then
                 		v_ids = gem.f_get_id_localizaciones(v_id_loc);
	                 	if(v_parametros.vista = 'loc') then
		                 	v_consulta = v_consulta || ' and loc.codigo != ''VEH''';
		                 elsif v_parametros.vista = 'vehiculos' then
		                 	v_consulta = v_consulta || ' and (loc.codigo in (''VEH'',''YPFB'')
									                 		   or loc.id_localizacion in ('||v_ids||') 
									                 		)';
		                 end if;
	                 end if;
                 end if;

				v_consulta = v_consulta || '  
                        ORDER BY loc.id_localizacion';
			
			--Devuelve la respuesta
			return v_consulta;
						
		end;    

	/*********************************    
 	#TRANSACCION:  'GEM_loc_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		rac	
 	#FECHA:		14-06-2012 03:48:41
	***********************************/

	elsif(p_transaccion='GEM_loc_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_localizacion)
					    from gem.tlocalizacion loc
					    inner join segu.tusuario usu1 on usu1.id_usuario = loc.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = loc.id_usuario_mod
					    where ';
			
			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************    
 	#TRANSACCION:  'GEM_GEO_SEL'
 	#DESCRIPCION:	Devuelve el listado de los datoa georeferenciados recursivamente a partir de un ID de la localizacion
 	#AUTOR:			RCM
 	#FECHA:			14-06-2012 03:48:41
	***********************************/

	elsif(p_transaccion='GEM_GEO_SEL') then

		begin

			--Verificar existencia del id
			if not exists(select 1 from gem.tlocalizacion
						where id_localizacion = v_parametros.id) then
				raise exception 'No se encuentran registros';
			end if;

			--Obtencion recursiva de ids
			v_ids = gem.f_get_id_localizaciones(v_parametros.id);


		end
					
	else
					     
		raise exception 'Transaccion inexistente';
					         
	end if;
					
EXCEPTION
					
	WHEN OTHERS THEN
			v_resp='';
			v_resp =  pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
			v_resp =  pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
			v_resp =  pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
			raise exception '%',v_resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;