CREATE OR REPLACE FUNCTION gem.f_uni_cons_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		SISTEMA DE GESTION DE MANTENIMIENTO
 FUNCION: 		gem.f_uni_cons_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'gem.tuni_cons'
 AUTOR: 		 (rac)
 FECHA:	        09-08-2012 00:42:57
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
	v_id_uni_cons	integer;
    v_id_uni_cons_comp integer;
    
    v_estado  varchar;
    v_id_uni_cons_padre integer;
    v_tipo_nodo varchar;
    v_id_tipo_equipo integer;
    g_registros record;
    v_resp_bool boolean;
    v_id_uni_cons_base integer;
    v_bool boolean;
    v_contador integer;
    g_registros2 record;
    v_resul varchar;
    v_resul_par varchar;
    v_vector_uni_cons INTEGER[];
    v_uni_cons_aux integer;
    v_tamano integer;
    v_i integer;
    v_codigo varchar;
    v_cod boolean;
    
    v_id_usuarios_tmp integer[];
	v_horas_dia integer;
   
BEGIN

    v_nombre_funcion = 'gem.f_uni_cons_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	if not pxp.f_existe_parametro(p_tabla,'horas_dia') then
		v_horas_dia = 24;
	else
		v_horas_dia = v_parametros.horas_dia;
	end if;
    
    /*********************************    
 	#TRANSACCION:  'GEM_TUC_INS'
 	#DESCRIPCION:	La interfaz funciona para TUC y UC
                    Insercion de Tipos de Unidades contructivas en estado borrador 
                    Inerta Unidad Contructivas
 	#AUTOR:	     	rac	
 	#FECHA:	    	09-08-2012 00:42:57
	***********************************/

	if(p_transaccion='GEM_TUC_INS')then
		     BEGIN     
             
            
			if (pxp.f_existe_parametro(p_tabla,'nombre_tipo_equipo')) then
                            
             	--verifie if "tipo_equipo" don't exist    
            	if v_parametros.id_tipo_equipo is null and v_parametros.nombre_tipo_equipo  != '' then
                	--search for other sames
                	select teq.id_tipo_equipo
                	into v_id_tipo_equipo  
                	from gem.ttipo_equipo  teq 
                	where teq.nombre ilike v_parametros.nombre_tipo_equipo  
                	and teq.estado_reg='activo' limit 1 offset 0;
                 
                   	if v_id_tipo_equipo is null then
						--insert new "tipo_equipo"
                        insert into gem.ttipo_equipo(
                        estado_reg,nombre,descripcion,codigo,id_usuario_reg,fecha_reg                   
                        ) values(
                        'activo',upper(v_parametros.nombre_tipo_equipo),'','reg indirecto',p_id_usuario,now()                   
                        )RETURNING id_tipo_equipo into v_id_tipo_equipo;  
                    end if;
				else
                	v_id_tipo_equipo = v_parametros.id_tipo_equipo;                   
                end if;

			else

                v_id_tipo_equipo = v_parametros.id_tipo_equipo;                   

            end if;

            --Verificar duplicidad de codigo de uo
            if exists(select distinct 1 
                   	from gem.tuni_cons uc 
                   	where lower(uc.codigo)=lower(v_parametros.codigo) and uc.estado_reg='activo') then
            	raise exception 'Código duplicado';
			end if;
                
			if(v_parametros.tipo = 'tuc') then
            	v_estado = 'borrador';  --falta validar
			else
				v_estado = 'registrado';  -- falta confirma el proceso de alta ,si ya no necesita modificaciones
			end if;
                
            --verifica el tipo_nodo
            if(v_parametros.id_uni_cons_padre='id') then
            	v_tipo_nodo='base';--carpetas agrupadores
            	v_id_uni_cons_padre=NULL;
            else
				--revismos el padre si es base entoces el nodo sera tipo raiz
                v_id_uni_cons_padre = (v_parametros.id_uni_cons_padre)::integer;
                
                select  uc.tipo_nodo
                into v_tipo_nodo
                from gem.tuni_cons uc
                where uc.id_uni_cons=v_id_uni_cons_padre;
                      
                if v_tipo_nodo = 'base' then
                	v_tipo_nodo = 'raiz';
                else
                	v_tipo_nodo = 'rama';
                end if;
                
			end if;
               
            --Registra primera unidad
            insert into gem.tuni_cons(
                estado_reg,
                estado,
                nombre,
                tipo,
                codigo,
                id_tipo_equipo,
               -- id_localizacion,
                id_usuario_reg,
                fecha_reg,
                fecha_mod,
                id_usuario_mod,
                tipo_nodo,
                herramientas_especiales,
                otros_datos_tec,
                funcion,
                punto_recepcion_despacho,
                horas_dia,
                ficha_tecnica
			) values(
                'activo',
                v_estado,
                upper(v_parametros.nombre),
                v_parametros.tipo,
                upper(v_parametros.codigo),
                v_id_tipo_equipo,
               -- v_parametros.id_localizacion,
                p_id_usuario,
                now(),
                null,
                null,
                v_tipo_nodo,
                v_parametros.herramientas_especiales,
                v_parametros.otros_datos_tec,
                v_parametros.funcion,
                v_parametros.punto_recepcion_despacho,
                v_horas_dia,
                v_parametros.ficha_tecnica
			)RETURNING id_uni_cons into v_id_uni_cons;
                
			-- si no es un nodo base registramo la relacion con el padre
               
            if(v_tipo_nodo != 'base')then

				insert into gem.tuni_cons_comp(
                    estado_reg,
                    opcional,
                    id_uni_cons_padre,
                    cantidad,
                    id_uni_cons_hijo,
                    id_usuario_reg,
                    fecha_reg,
                    id_usuario_mod,
                    fecha_mod
				) values(
                	'activo',
                    'no',
                    v_id_uni_cons_padre,
                    '1',
                    v_id_uni_cons,
                    p_id_usuario,
                    now(),
                    null,
                    null
				)RETURNING id_uni_cons_comp into v_id_uni_cons_comp;
               
			end if;
               
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Equipos almacenado(a) con exito (id_uni_cons'||v_id_uni_cons||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_uni_cons',v_id_uni_cons::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'GEM_TUC_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		rac	
 	#FECHA:		09-08-2012 00:42:57
	***********************************/

	elsif(p_transaccion='GEM_TUC_MOD')then

		begin
             
            IF (pxp.f_existe_parametro(p_tabla,'nombre_tipo_equipo')) THEN
                            
             --verifie if "tipo_equipo" don't exist    
              
             if v_parametros.id_tipo_equipo is null and v_parametros.nombre_tipo_equipo  != '' then
                 --search for other sames
                 select teq.id_tipo_equipo into v_id_tipo_equipo  
                 from gem.ttipo_equipo  teq 
                 where teq.nombre ilike v_parametros.nombre_tipo_equipo  
                 and teq.estado_reg='activo' limit 1 offset 0;
                 
                   if v_id_tipo_equipo is null then
                           --insert new "tipo_equipo"
                             insert into gem.ttipo_equipo(
                            estado_reg,
                            nombre,
                            descripcion,
                            codigo,
                            id_usuario_reg,
                            fecha_reg                   
                            ) values(
                            'activo',
                            upper(v_parametros.nombre_tipo_equipo),
                            '',
                            'reg indirecto',
                            p_id_usuario,
                            now()                   
                            )RETURNING id_tipo_equipo into v_id_tipo_equipo;  
                     
                      end if;
                  else
                       v_id_tipo_equipo = v_parametros.id_tipo_equipo;                   
                  end IF;
             else
                v_id_tipo_equipo = v_parametros.id_tipo_equipo;                   
             END IF;
        
         -- verificar duplicidad de codigo de uo
               if exists (
                   select distinct 1 
                   from gem.tuni_cons uc 
                   where lower(uc.codigo)=lower(v_parametros.codigo) 
                   and uc.estado_reg='activo' and uc.id_uni_cons!=v_parametros.id_uni_cons) then
                   raise exception 'CODIGO DUPLICADO';
                end if;
        
			--Sentencia de la modificacion
			update    gem.tuni_cons set
			nombre = upper(v_parametros.nombre),
			codigo = upper(v_parametros.codigo),
			id_tipo_equipo = v_id_tipo_equipo,
			--id_localizacion = v_parametros.id_localizacion,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
            herramientas_especiales = v_parametros.herramientas_especiales,
            otros_datos_tec = v_parametros.otros_datos_tec,
            funcion = v_parametros.funcion,
            punto_recepcion_despacho = v_parametros.punto_recepcion_despacho,
            ficha_tecnica = v_parametros.ficha_tecnica
			where id_uni_cons=v_parametros.id_uni_cons;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Equipos modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_uni_cons',v_parametros.id_uni_cons::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;
    /*********************************    
 	#TRANSACCION:  'GEM_TUCGENCAL_MOD'
 	#DESCRIPCION:	Modificacion la unidad construtiva para incluir o no incluir en la generacion de calendario
 	#AUTOR:		rac	
 	#FECHA:		09-08-2012 00:42:57
	***********************************/

	elsif(p_transaccion='GEM_TUCGENCAL_MOD')then

		begin
            
        
        
        
            
        
			--Sentencia de la modificacion
			update    gem.tuni_cons set
			incluir_calgen = v_parametros.incluir_calgen,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario
			where id_uni_cons=v_parametros.id_uni_cons;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Modifica la inclusion en el calendario apra la unidad'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_uni_cons',v_parametros.id_uni_cons::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;
    
    
        
    /*********************************    
 	#TRANSACCION:  'GEM_TUCESTBL_MOD'
 	#DESCRIPCION:	Bloquea y Desbloquea, solo se utiliza para el tuc tipo raiz
 	#AUTOR:		rac	
 	#FECHA:		09-08-2012 00:42:57
	***********************************/

	elsif(p_transaccion='GEM_TUCESTBL_MOD')then

		begin
            -- get node data
        
            select uc.estado, uc.tipo_nodo 
              into v_estado ,v_tipo_nodo 
            from gem.tuni_cons uc where uc.id_uni_cons = v_parametros.id_uni_cons ;
           
            --then, check the node type root
            if v_tipo_nodo!='raiz' then
            
                raise exception 'Solo pueden ser bloqueados los nodos Raiz';
            
            end if;
            
            --then check the current status,  aprobrado for block or borrador for aunblock
            
            if v_estado  = v_parametros.estado then
             raise exception 'El equipo ya se encuentra en estado %',v_estado;
            
            end if;
          
			--Sentencia de la modificacion
			update    gem.tuni_cons set
			estado = v_parametros.estado,			
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario
			where id_uni_cons=v_parametros.id_uni_cons;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Equipos modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_uni_cons',v_parametros.id_uni_cons::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;
        
              
    /*********************************    
 	#TRANSACCION:  'GEM_ADDUC_MOD'
 	#DESCRIPCION:	Generate unicons from template tipo_cuni_cons
 	#AUTOR:		rac	
 	#FECHA:		09-08-2012 00:42:57
	***********************************/
    
     elseif(p_transaccion='GEM_ADDUC_MOD')then
		     BEGIN   
             
             
            
             /*
             1) busca los datos del primer nodo raiz
             2)  los inserta con UC
             3) busca recursivamente hijos pxp.f_addunicon_recursivo
             */
             
             
             
             --el codigo tiene que ser unico para la UC
             
             
             IF exists( select 1 from  gem.tuni_cons  uc 
                      where  upper(uc.codigo) = upper(v_parametros.codigo_uni_cons )) THEN
              raise exception 'El codigo de equipo ya existe';
             END IF;
              
             
             --0) revisa si el lugar ya tiene un nodo creado desde el campo id_plantilla
             
             select tuc.id_uni_cons  into v_id_uni_cons_base 
             from gem.tuni_cons  tuc 
             where tuc.id_localizacion = v_parametros.id_localizacion  
             and tuc.estado_reg='activo' 
             and tuc.tipo_nodo = 'base';
             
             
             if  v_id_uni_cons_base is null then
             
               -- insertamos nodo base
                --Sentencia de la insercion
                        insert into gem.tuni_cons(
                        estado_reg,
                        estado,
                        nombre,
                        tipo,
                        codigo,
                        id_usuario_reg,
                        fecha_reg,
                        id_localizacion,
                        tipo_nodo
                        ) values(
                        'activo',
                       'registrado',
                        upper(v_parametros.nombre),
                        'uc',
                        upper(v_parametros.codigo_localizacion),
                        p_id_usuario,
                        now(),
                        v_parametros.id_localizacion,
                        'base'
                        )RETURNING id_uni_cons into v_id_uni_cons_base;
             
              end if;
             
             
             
             
             -- 1) busca los datos del primer nodo raiz
             
			FOR g_registros in (
            	select  
                	tuc.codigo,
                    tuc.estado,
                    tuc.id_tipo_equipo,
                    tuc.nombre,
                    tuc.tipo_nodo,
                    tuc.id_uni_cons,
                    tuc.herramientas_especiales,
                    tuc.otros_datos_tec,
                    tuc.funcion,
                    tuc.punto_recepcion_despacho,
                    tuc.ficha_tecnica
                from gem.tuni_cons tuc 
                where tuc.id_uni_cons = v_parametros.id_uni_cons  
                and tuc.estado_reg='activo'
			) LOOP
               
             --  busca los usarios con acceso a esta nueva_unidad constructiva
             
              WITH RECURSIVE arbol (id_localizacion,id_localizacion_fk) AS (  
                        select lo.id_localizacion,lo.id_localizacion_fk
                        from gem.tlocalizacion lo
                        where lo.id_localizacion = v_parametros.id_localizacion
                      UNION ALL
                         SELECT lo2.id_localizacion, lo2.id_localizacion_fk
                         FROM arbol a
                         INNER JOIN gem.tlocalizacion lo2 on lo2.id_localizacion =  a.id_localizacion_fk)  
                                       
                                       
               SELECT pxp.aggarray( lu.id_usuario) into v_id_usuarios_tmp
              FROM arbol a 
              inner join gem.tlocalizacion_usuario lu on lu.id_localizacion = a.id_localizacion; 
                
                -- 2)  los inserta como UC
                      
                       --Sentencia de la insercion
						insert into gem.tuni_cons (
                        	estado_reg,
                        	estado,
                        	nombre,
                        	tipo,
                        	codigo,
                        	id_tipo_equipo,
                        	id_usuario_reg,
                        	fecha_reg,
                        	id_localizacion,
                        	tipo_nodo,
                         	id_plantilla,
                         	id_usuarios,
                            herramientas_especiales,
                            otros_datos_tec,
                            funcion,
                            punto_recepcion_despacho,
                            ficha_tecnica
                        ) values (
                        	'activo',
                       		'registrado',
                        	upper(g_registros.nombre),
                        	'uc',
                        	upper(v_parametros.codigo_uni_cons),
                        	g_registros.id_tipo_equipo,
                        	p_id_usuario,
                        	now(),
                        	v_parametros.id_localizacion,
                        	'raiz',
                         	v_parametros.id_uni_cons,
                         	v_id_usuarios_tmp,
                            g_registros.herramientas_especiales,
                            g_registros.otros_datos_tec,
                            g_registros.funcion,
                            g_registros.punto_recepcion_despacho,
                            g_registros.ficha_tecnica
                        ) RETURNING id_uni_cons into v_id_uni_cons;
                        
                        
                     --insertamos la relacion con el nodo relacionador
                     
                      --inseta la relacion con el padre    
                    
                
                
            insert into gem.tuni_cons_comp(
                        estado_reg,
                        opcional,
                        id_uni_cons_padre,
                        cantidad,
                        id_uni_cons_hijo,
                        id_usuario_reg,
                        fecha_reg,
                        id_usuario_mod,
                        fecha_mod
                        ) values(
                        'activo',
                        'no',
                        v_id_uni_cons_base,  --padre
                        '1',
                        v_id_uni_cons,--hijo
                        p_id_usuario,
                        now(),
                        null,
                        null
                        )RETURNING id_uni_cons_comp into v_id_uni_cons_comp; 
                        
                   
           
                        
      END LOOP;
            -- 3) busca recursivamente hijos pxp.f_addunicon_recursivo
      
             v_resp_bool = gem.f_addunicon_recursivo(v_parametros.id_uni_cons,v_id_uni_cons,p_id_usuario,'raiz',v_parametros.codigo_uni_cons);
             
            -- 3.1) llamada a la clonacion de datos
            
             -- LLAMADA A LA FUNCION CLONAR DATOS UNICONS  PARA 
                    --  v_id_uni_cons              CLONADO
                    --  v_parametros.id_uni_cons   ORIGINAL      
                    --  p_id_usuario
      
             v_resp_bool = gem.f_clon_unicons(g_registros.id_uni_cons ,v_id_uni_cons,p_id_usuario);
          
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Dulicado desde plantilla  el uni_con  (id_uni_cons'||v_id_uni_cons||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_uni_cons',v_id_uni_cons::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;   
        
/*********************************    
 	#TRANSACCION:  'GEM_VERCALGEN_MOD'
 	#DESCRIPCION:	Verifica si tiene calendario generados para lanzar un mensaje 
                     de advertencia si no tiene procesde con la generacion
 	#AUTOR:		rac	KPLIAN
 	#FECHA:		09-08-2012 00:42:57
	***********************************/
    
     elseif(p_transaccion='GEM_VERCALGEN_MOD')then
		     BEGIN   
             
              --iniciamos el contador de unidades procesadas
              v_contador=0;
              v_resul ='';
              
             
             --------------------
             -- (1) VERIFICACION PARA GENERAR EL CALENDARIO
             --------------------
             --si es el nodo es de tipo localizacion
             if v_parametros.tipo_nodo <> 'uni_cons' and v_parametros.tipo_nodo <> 'rama' THEN
                   -- 1)  busca recursivamente los equipos que correponden a la localizacion indicada
                    --rcm 21/09/2013: se aumenta inner join con tmant_predef    
                    FOR g_registros in  (
                     WITH RECURSIVE sub_localizacion(id_localizacion, id_localizacion_fk, nombre) 
                          AS (
                              SELECT id_localizacion, id_localizacion_fk, nombre 
                              FROM gem.tlocalizacion WHERE id_localizacion =  v_parametros.id_localizacion
                                UNION ALL
                              SELECT l.id_localizacion, l.id_localizacion_fk, l.nombre
                              FROM sub_localizacion subl, gem.tlocalizacion l
                              WHERE l.id_localizacion_fk = subl.id_localizacion
                             )
                          SELECT unicon.id_uni_cons,unicon.codigo
                          FROM gem.tuni_cons unicon 
                          WHERE unicon.id_localizacion 
                          IN ( SELECT id_localizacion FROM sub_localizacion ORDER BY id_localizacion) 
                          AND unicon.tipo_nodo ilike 'raiz' and unicon.tipo = 'uc' and unicon.estado_reg = 'activo'
                          AND unicon.id_uni_cons in (select id_uni_cons from gem.tuni_cons_mant_predef)) LOOP
                   
                       --por cada unidad raiz vamos verificar sus partes para determinar si es necesario generar el calendario
                   
                   
                     --rcm 21/09/2013: se aumenta inner join con tmant_predef    
                          FOR g_registros2 in (
                               WITH RECURSIVE arbol (id_uni_cons,id_uni_cons_padre, id_uni_cons_hijo, codigo, incluir_calgen) AS (  
                                   select  uc.id_uni_cons, ucc.id_uni_cons_padre, ucc.id_uni_cons_hijo,
                                   uc.codigo , uc.incluir_calgen
                                   from gem.tuni_cons uc
                                   left join gem.tuni_cons_comp ucc 
                                   on ucc.id_uni_cons_padre = uc.id_uni_cons
                                   where uc.id_uni_cons = g_registros.id_uni_cons
                                   and uc.id_uni_cons in (select id_uni_cons from gem.tuni_cons_mant_predef)   
                                   UNION ALL  
                                   SELECT uc2.id_uni_cons, ucc2.id_uni_cons_padre, ucc2.id_uni_cons_hijo,
                                   uc2.codigo ,uc2.incluir_calgen  
                                   FROM arbol a 
                                   INNER JOIN gem.tuni_cons uc2 ON uc2.id_uni_cons = a.id_uni_cons_hijo
                                   LEFT JOIN gem.tuni_cons_comp ucc2 on ucc2.id_uni_cons_padre = uc2.id_uni_cons
                                   WHERE uc2.id_uni_cons in (select id_uni_cons from gem.tuni_cons_mant_predef))  
                               SELECT distinct id_uni_cons, codigo,incluir_calgen FROM arbol 
                           ) LOOP
                                
                                 --    llama a una funcion para generar el calendaio para el equipo indicado
                                IF g_registros2.incluir_calgen THEN
                                
                                     v_resul_par='';
                                     
                                     
                                     v_vector_uni_cons=  array_append(v_vector_uni_cons,g_registros2.id_uni_cons);
                   
                   					--Función que verifica si el equipo y mantenimiento ya tienen generado un mantenimiento
                                     v_resul_par = gem.f_verifica_calendario_equipo (g_registros2.id_uni_cons,v_parametros.fecha_fin,p_id_usuario);
                       
                                     IF(v_resul_par!=''  )THEN
                                         IF(v_resul=''  ) THEN
                                            v_resul = '-- '|| g_registros.codigo||'-- ['|| v_resul_par||']\n';
                                         ELSE
                                             v_resul = v_resul||'-- '|| g_registros.codigo||'-- ['||v_resul_par||']\n';
                                         END IF;
                                                               
                                     END IF;
                                     
                                     v_contador = v_contador + 1;

                                 END IF;
                  
             				END LOOP;
             
             
                END LOOP;
             
             ELSEIF    v_parametros.tipo_nodo = 'uni_cons' THEN
              --si no es localizacion es la unidad constructiva,buscamos recursivamente las partes que entran al calendario
                 
                FOR g_registros in (
                     WITH RECURSIVE arbol (id_uni_cons,id_uni_cons_padre, id_uni_cons_hijo, codigo, incluir_calgen) AS (  
                         select  uc.id_uni_cons, ucc.id_uni_cons_padre, ucc.id_uni_cons_hijo, uc.codigo , uc.incluir_calgen

                          from gem.tuni_cons uc
                          LEFT join  gem.tuni_cons_comp ucc 
                                      on ucc.id_uni_cons_padre = uc.id_uni_cons
                          where    uc.id_uni_cons = v_parametros.id_uni_cons   
                            UNION ALL  
                             SELECT uc2.id_uni_cons, ucc2.id_uni_cons_padre, ucc2.id_uni_cons_hijo,uc2.codigo ,uc2.incluir_calgen  
                              FROM arbol a 
                              INNER JOIN  gem.tuni_cons uc2   ON uc2.id_uni_cons = a.id_uni_cons_hijo
                              LEFT JOIN   gem.tuni_cons_comp ucc2 on ucc2.id_uni_cons_padre = uc2.id_uni_cons
                            )  
                           SELECT distinct id_uni_cons, codigo,incluir_calgen FROM arbol 
              
              
                 ) LOOP
                 
                     IF g_registros.incluir_calgen THEN
                     
                     	v_vector_uni_cons= array_append( v_vector_uni_cons,g_registros.id_uni_cons);
              
                         v_resul_par =  gem.f_verifica_calendario_equipo (g_registros.id_uni_cons,v_parametros.fecha_fin,p_id_usuario);
                        
                        
                         IF(v_resul_par!=''  )THEN
                               IF(v_resul=''  )THEN
                                  v_resul = '['||v_resul_par||']\n';
                               ELSE
                                   v_resul = v_resul||''||'['||v_resul_par||']\n';
                               END IF;
                          END IF;
                        
                       
                       v_contador = v_contador + 1;
                     END IF; 
             
             
                END LOOP;
             ELSE
              --si no es localizacion es la unidad constructiva, generamos directamente
                     
                     v_vector_uni_cons= array_append(v_vector_uni_cons,v_parametros.id_uni_cons);
                     v_resul_par =  gem.f_verifica_calendario_equipo (v_parametros.id_uni_cons,v_parametros.fecha_fin,p_id_usuario);
               
                     IF(v_resul_par!=''  )THEN
                          IF(v_resul=''  )THEN
                             v_resul = v_resul_par;
                          ELSE
                             v_resul = v_resul||','||v_resul_par;
                          END IF;
                      END IF;
            
     			if exists(select 1 from gem.tuni_cons_mant_predef
                		where id_uni_cons = v_parametros.id_uni_cons) then
               		v_contador = v_contador + 1;   
                end if;
             
             END IF;
             
             --armar un vector de uni_cons
            
            ---------------------------
            -- (2)GENERACIÓN DEL CALENDARIO
            ---------------------------
            
            -- Verificamos si existen equipos con  mantenimientos planificados en estas fechas
			IF  v_resul!='' THEN

            	--Existen, por lo tanto se despliega el detalle de los equipos afectados
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','La verificacion se ha realizado con exito, existen calendarios '||v_resul); 
                v_resp = pxp.f_agrega_clave(v_resp,'unidades',v_resul); 
                v_resp = pxp.f_agrega_clave(v_resp,'generado','false');
                v_resp = pxp.f_agrega_clave(v_resp,'contador',v_contador::varchar); 
           
			ELSE
            	--No existen, por lo tanto se genera el calendario
               	v_tamano = coalesce(array_length(v_vector_uni_cons, 1),0);
              
            	FOR v_i IN 1..v_tamano LOOP
                  
                	v_bool =  gem.f_genera_calendario_equipo (v_vector_uni_cons[v_i],v_parametros.fecha_fin,p_id_usuario);
                                                 
					IF NOT v_bool THEN
                    	raise exception 'Error al generar calendario para %',g_registros.id_uni_cons;
                    END IF;  
                   
                END LOOP;
                    
                 -- 3) Retorna exito
                 v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Verificado y Generado el calendario para la localizaciones'); 
                 v_resp = pxp.f_agrega_clave(v_resp,'generado','true');
                 v_resp = pxp.f_agrega_clave(v_resp,'contador',v_contador::varchar); 
                
            END IF;
            
            --------------------- 
            -- (3)RESPUESTA
            ---------------------
			return v_resp;
     
		END;



 /*********************************    
 	#TRANSACCION:  'GEM_GENCAL_MOD'
 	#DESCRIPCION:	Genera calendario para los quipos seleccionados
 	#AUTOR:		rac	KPLIAN
 	#FECHA:		09-08-2012 00:42:57
	***********************************/
    
     elseif(p_transaccion='GEM_GENCAL_MOD')then
		     BEGIN   
             
              --iniciamos el contador de unidades procesadas
              v_contador=0;
             
             --si es el nodo es de tipo localizacion
             if v_parametros.tipo_nodo <> 'uni_cons' and v_parametros.tipo_nodo <> 'rama' THEN
                   -- 1)  busca recursivamente los equipos que correponden a la localizacion indicada
                    --rcm 21/09/2013: se aumenta inner join con tmant_predef       
                    FOR g_registros in  (
                     WITH RECURSIVE sub_localizacion(id_localizacion, id_localizacion_fk, nombre) 
                          AS (
                              SELECT id_localizacion, id_localizacion_fk, nombre 
                              FROM gem.tlocalizacion WHERE id_localizacion =  v_parametros.id_localizacion
                                UNION ALL
                              SELECT l.id_localizacion, l.id_localizacion_fk, l.nombre
                              FROM sub_localizacion subl, gem.tlocalizacion l
                              WHERE l.id_localizacion_fk = subl.id_localizacion
                             )
                          SELECT unicon.id_uni_cons
                          FROM gem.tuni_cons unicon 
                          WHERE unicon.id_localizacion 
                          IN ( SELECT id_localizacion FROM sub_localizacion ORDER BY id_localizacion) 
                          AND unicon.tipo_nodo ilike 'raiz' and unicon.tipo = 'uc' and unicon.estado_reg = 'activo'
                          AND unicon.id_uni_cons in (select id_uni_cons from gem.tuni_cons_mant_predef)) LOOP
                   
                       --por cada unidad raiz vamos verificar sus partes para determinar si es necesario generar el calendario
                   
                   
                     		--rcm 21/09/2013: se aumenta inner join con tmant_predef    
                          FOR g_registros2 in (
                               WITH RECURSIVE arbol (id_uni_cons,id_uni_cons_padre, id_uni_cons_hijo, codigo, incluir_calgen) AS (  
                                   select  uc.id_uni_cons, ucc.id_uni_cons_padre, ucc.id_uni_cons_hijo, uc.codigo , uc.incluir_calgen
                                    from gem.tuni_cons uc
                                    LEFT join  gem.tuni_cons_comp ucc 
                                    on ucc.id_uni_cons_padre = uc.id_uni_cons
                                    where uc.id_uni_cons = g_registros.id_uni_cons   
                                    and uc.id_uni_cons in (select id_uni_cons from gem.tuni_cons_mant_predef)
                                      UNION ALL  
                                       SELECT uc2.id_uni_cons, ucc2.id_uni_cons_padre, ucc2.id_uni_cons_hijo,uc2.codigo ,uc2.incluir_calgen  
                                        FROM arbol a 
                                        INNER JOIN  gem.tuni_cons uc2   ON uc2.id_uni_cons = a.id_uni_cons_hijo
                                        LEFT JOIN   gem.tuni_cons_comp ucc2 on ucc2.id_uni_cons_padre = uc2.id_uni_cons
                                        where uc2.id_uni_cons in (select id_uni_cons from gem.tuni_cons_mant_predef)
                                      )  
                                     SELECT distinct id_uni_cons, codigo,incluir_calgen FROM arbol 
                        
                        
                           ) LOOP
                                
                                 --    llama a una funcion para generar el calendaio para el equipo indicado
                                IF g_registros2.incluir_calgen THEN
                   
                                     v_bool =  gem.f_genera_calendario_equipo (g_registros2.id_uni_cons,v_parametros.fecha_fin,p_id_usuario);
                       
                                     v_contador = v_contador + 1;
                          
                                    IF NOT v_bool THEN
                                     raise exception 'Error al generar calendario para %',g_registros.id_uni_cons;
                                    END IF;
                                    
                                 END IF;
                  
             				END LOOP;
             
             
                END LOOP;
             
             ELSEIF    v_parametros.tipo_nodo = 'uni_cons' THEN
              --si no es localizacion es la unidad constructiva,buscamos recursivamente las partes que entran al calendario
                 
                FOR g_registros in (
                     WITH RECURSIVE arbol (id_uni_cons,id_uni_cons_padre, id_uni_cons_hijo, codigo, incluir_calgen) AS (  
                         select  uc.id_uni_cons, ucc.id_uni_cons_padre, ucc.id_uni_cons_hijo, uc.codigo , uc.incluir_calgen

                          from gem.tuni_cons uc
                          LEFT join  gem.tuni_cons_comp ucc 
                                      on ucc.id_uni_cons_padre = uc.id_uni_cons
                          where    uc.id_uni_cons = v_parametros.id_uni_cons   
                            UNION ALL  
                             SELECT uc2.id_uni_cons, ucc2.id_uni_cons_padre, ucc2.id_uni_cons_hijo,uc2.codigo ,uc2.incluir_calgen  
                              FROM arbol a 
                              INNER JOIN  gem.tuni_cons uc2   ON uc2.id_uni_cons = a.id_uni_cons_hijo
                              LEFT JOIN   gem.tuni_cons_comp ucc2 on ucc2.id_uni_cons_padre = uc2.id_uni_cons
                            )  
                           SELECT distinct id_uni_cons, codigo,incluir_calgen FROM arbol 
              
              
                 ) LOOP
                 
                     IF g_registros.incluir_calgen THEN

                         v_bool =  gem.f_genera_calendario_equipo (g_registros.id_uni_cons,v_parametros.fecha_fin,p_id_usuario);
                        
                        IF NOT v_bool THEN
                         raise exception 'Error al generar calendario para %',g_registros.id_uni_cons;
                        END IF;
                     
                       v_contador = v_contador + 1;
                     END IF; 
             
             
                END LOOP;
             ELSE
              --si no es localizacion es la unidad constructiva, generamos directamente
               v_bool =  gem.f_genera_calendario_equipo (v_parametros.id_uni_cons,v_parametros.fecha_fin,p_id_usuario);
               
               if exists(select 1 from gem.tuni_cons_mant_predef
                		where id_uni_cons = v_parametros.id_uni_cons) then
               		v_contador = v_contador + 1;   
                end if;
             
             END IF;
             
             
             -- 3) retonra exito
             v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Generando el calendario para la localizacion : '|| v_parametros.id_localizacion ); 

              v_resp = pxp.f_agrega_clave(v_resp,'generado','true');
             v_resp = pxp.f_agrega_clave(v_resp,'contador',v_contador::varchar); 
              
             return v_resp;
     

            END;

	/*********************************    
 	#TRANSACCION:  'GEM_TUC_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		rac	
 	#FECHA:		09-08-2012 00:42:57
	***********************************/

	elsif(p_transaccion='GEM_TUC_ELI')then

		begin
        
           --0)  verifica el estado solo puede eliminar cuando el estado es borrador
           if exists ( select DISTINCT 1 
                          from gem.tuni_cons uc
                          where uc.estado != 'borrador' 
                          and   uc.id_uni_cons = v_parametros.id_uni_cons) then
               
                             
                        raise exception 'Eliminacion no realizada. El elemento que quiere eliminar no se encuentra en estado borrador';
              
              end if;
           
               
        
        
            --1) verificamos si tiene relaciones activas con sus hijos (asumimos que si tiene hijos tendra relaciones activas con ellos)
            
            if exists ( select DISTINCT 1 
                          from gem.tuni_cons uc
                          inner join  gem.tuni_cons_comp ucc  on uc.id_uni_cons = ucc.id_uni_cons_padre   and ucc.estado_reg='activo' 
                          where uc.id_uni_cons = v_parametros.id_uni_cons) then
               
                        --NOTA) sera necesario adicionar  una trsaccion que realize una eliminacion recursiva
                        --      previa confirmacion del usuario despues de este error 
                             
                        raise exception 'Eliminacion no realizada. El elemento que quiere eliminar tienes subpartes anidadas';
              
              end if;
              
                             
               --2) inactiva la unidad
               update gem.tuni_cons 
               set estado_reg='inactivo'
               where id_uni_cons= v_parametros.id_uni_cons;
               
               
               -- 3) inactiva las relaciones con los padres (para que se cumpla siempre la regla en 1)
               update gem.tuni_cons_comp
               set estado_reg='inactivo'
               where id_uni_cons_hijo=v_parametros.id_uni_cons;
              
  
		               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Equipos eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_uni_cons',v_parametros.id_uni_cons::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;
		
	/*********************************    
 	#TRANSACCION:  'GEM_INACUC_UPD'
 	#DESCRIPCION:	Inactiva unidad constructiva
 	#AUTOR:		rcm	
 	#FECHA:		04-12-2012
	***********************************/

	elsif(p_transaccion='GEM_INACUC_UPD')then

		begin
            --Verificación de existencia del registro
            if not exists(select 1 from gem.tuni_cons
            		where id_uni_cons = v_parametros.id_uni_cons) then
				raise exception 'Equipo no encontrado';            		
            end if;
            
            --Inactivar la Unidad Constructiva
            update gem.tuni_cons set
            estado_reg = 'inactivo',
            id_usuario_mod = p_id_usuario,
            fecha_mod = now()
            where id_uni_cons = v_parametros.id_uni_cons RETURNING codigo into v_codigo;
            
            --PRocesa el código para aumentarle un prefijo y mantener el rastro de su código, para liberar el código original
            v_cod=false;
            if not exists (select 1 from gem.tuni_cons where codigo = 'del1_'||v_codigo) then
            	v_codigo = 'del1_'||v_codigo;
            	v_cod = true;
            elsif not exists (select 1 from gem.tuni_cons where codigo = 'del2_'||v_codigo) then
            	v_codigo = 'del2_'||v_codigo;
            	v_cod = true;
            elsif not exists (select 1 from gem.tuni_cons where codigo = 'del3_'||v_codigo) then
            	v_codigo = 'del3_'||v_codigo;
            	v_cod = true;
            elsif not exists (select 1 from gem.tuni_cons where codigo = 'del4_'||v_codigo) then
            	v_codigo = 'del4_'||v_codigo;
            	v_cod = true;
            elsif not exists (select 1 from gem.tuni_cons where codigo = 'del5_'||v_codigo) then
            	v_codigo = 'del5_'||v_codigo;
            	v_cod = true;
            end if;
            
            if not v_cod then
            	raise exception 'No se pudo eliminar el equipo: se superó el límite máximo para almacenar el histórico del código';
            end if;
            
            update gem.tuni_cons set
            codigo = v_codigo
            where id_uni_cons = v_parametros.id_uni_cons;
             
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Equipos eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_uni_cons',v_parametros.id_uni_cons::varchar);
               
            --Devuelve la respuesta
            return v_resp;
		end;
		
	/*********************************    
 	#TRANSACCION:  'GM_EQGRAL_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:			rcm	
 	#FECHA:			16/02/2012
	***********************************/

	elsif(p_transaccion='GM_EQGRAL_MOD')then

		begin
			--Sentencia de la modificacion
			update gem.tuni_cons set
			--codigo = v_parametros.codigo,
			incluir_calgen = v_parametros.incluir_calgen,
			otros_datos_tec = v_parametros.otros_datos_tec,
			punto_recepcion_despacho = v_parametros.punto_recepcion_despacho,
			herramientas_especiales = v_parametros.herramientas_especiales,
			estado = v_parametros.estado,
			nombre = v_parametros.nombre,
			funcion = v_parametros.funcion,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now()
			where id_uni_cons=v_parametros.id_uni_cons;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Equipos modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_uni_cons',v_parametros.id_uni_cons::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'GM_EQUIPO_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		05-02-2013 07:24:57
	***********************************/

	elsif(p_transaccion='GM_EQUIPO_ELI')then

		begin
			--Sentencia de la eliminacion
			update gem.tuni_cons set
			estado_reg = 'inactivo'
            where id_uni_cons=v_parametros.id_uni_cons;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Equipos eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_uni_cons',v_parametros.id_uni_cons::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;
		
	/*********************************    
 	#TRANSACCION:  'GEM_EQGRAL_MOD'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:			rcm	
 	#FECHA:			07/03/2013
	***********************************/

	elsif(p_transaccion='GEM_EQGRAL_MOD')then

		begin
			--Sentencia de la eliminacion
			update gem.tuni_cons set
			incluir_calgen = v_parametros.incluir_calgen,
			otros_datos_tec = v_parametros.otros_datos_tec,
			punto_recepcion_despacho = v_parametros.punto_recepcion_despacho,
			herramientas_especiales = v_parametros.herramientas_especiales,
			nombre = v_parametros.nombre,
			funcion = v_parametros.funcion,
			horas_dia = v_horas_dia,
			tipo_unicons = v_parametros.tipo_unicons,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now()
			where id_uni_cons=v_parametros.id_uni_cons;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Equipo actualzado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_uni_cons',v_parametros.id_uni_cons::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;
		
	/*********************************    
 	#TRANSACCION:  'GEM_CAMCOD_MOD'
 	#DESCRIPCION:	Cambio de código del equipo
 	#AUTOR:		RCM
 	#FECHA:		03/07/2014
	***********************************/

	elsif(p_transaccion='GEM_CAMCOD_MOD')then

		begin
			--Verifica si el código es nuevo
			if exists(select 1 from gem.tuni_cons
					where id_uni_cons = v_parametros.id_uni_cons
					and codigo = v_parametros.codigo_uni_cons) then
				raise exception 'El código nuevo es igual al anterior. Nada que hacer.';
			end if;
			
			--Sentencia de la eliminacion
			update gem.tuni_cons set
			codigo = v_parametros.codigo_uni_cons,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario
            where id_uni_cons=v_parametros.id_uni_cons;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Cambio de código del equipo realizado'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_uni_cons',v_parametros.id_uni_cons::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;


  /*******************************************/
  /*******************************************/

  /*********************************    
  #TRANSACCION: 'GEM_ADDUCLOC_MOD'
  #DESCRIPCION: Aumentar planillas a equipos en localizacion
  #AUTOR:       rcm 
  #FECHA:       24/09/2016
  ***********************************/
    
     elseif(p_transaccion='GEM_ADDUCLOC_MOD')then
         BEGIN   
             
             
            
             /*
             1) busca los datos del primer nodo raiz
             2)  los inserta con UC
             3) busca recursivamente hijos pxp.f_addunicon_recursivo
             */
             
            
             -- 1) busca los datos del primer nodo raiz
             
      FOR g_registros in (
              select  
                  tuc.codigo,
                    tuc.estado,
                    tuc.id_tipo_equipo,
                    tuc.nombre,
                    tuc.tipo_nodo,
                    tuc.id_uni_cons,
                    tuc.herramientas_especiales,
                    tuc.otros_datos_tec,
                    tuc.funcion,
                    tuc.punto_recepcion_despacho,
                    tuc.ficha_tecnica,
                    tuc.id_localizacion
                from gem.tuni_cons tuc 
                where tuc.id_uni_cons = v_parametros.id_plantilla  
                and tuc.estado_reg='activo'
      ) LOOP
               
                
                -- 2)  los inserta como UC
                      
                       --Sentencia de la insercion
            v_codigo = g_registros.codigo;
            insert into gem.tuni_cons (
                          estado_reg,
                          estado,
                          nombre,
                          tipo,
                          codigo,
                          id_tipo_equipo,
                          id_usuario_reg,
                          fecha_reg,
                          id_localizacion,
                          tipo_nodo,
                          id_plantilla,
                            herramientas_especiales,
                            otros_datos_tec,
                            funcion,
                            punto_recepcion_despacho,
                            ficha_tecnica
                        ) values (
                          'activo',
                          'registrado',
                          upper(g_registros.nombre),
                          'uc',
                          upper(g_registros.codigo),
                          g_registros.id_tipo_equipo,
                          p_id_usuario,
                          now(),
                          g_registros.id_localizacion,
                          'raiz',
                          v_parametros.id_plantilla,
                            g_registros.herramientas_especiales,
                            g_registros.otros_datos_tec,
                            g_registros.funcion,
                            g_registros.punto_recepcion_despacho,
                            g_registros.ficha_tecnica
                        ) RETURNING id_uni_cons into v_id_uni_cons;
                        
                        
                     --insertamos la relacion con el nodo relacionador
                     
                      --inseta la relacion con el padre    
                    
                
                
            insert into gem.tuni_cons_comp(
                        estado_reg,
                        opcional,
                        id_uni_cons_padre,
                        cantidad,
                        id_uni_cons_hijo,
                        id_usuario_reg,
                        fecha_reg,
                        id_usuario_mod,
                        fecha_mod
                        ) values(
                        'activo',
                        'no',
                        v_parametros.id_uni_cons,  --padre
                        '1',
                        v_id_uni_cons,--hijo
                        p_id_usuario,
                        now(),
                        null,
                        null
                        )RETURNING id_uni_cons_comp into v_id_uni_cons_comp; 
                        
                   
           
                        
      END LOOP;
            -- 3) busca recursivamente hijos pxp.f_addunicon_recursivo
      
             v_resp_bool = gem.f_addunicon_recursivo(v_parametros.id_plantilla,v_id_uni_cons,p_id_usuario,'raiz',v_codigo);
             
            -- 3.1) llamada a la clonacion de datos
            
             -- LLAMADA A LA FUNCION CLONAR DATOS UNICONS  PARA 
                    --  v_id_uni_cons              CLONADO
                    --  v_parametros.id_uni_cons   ORIGINAL      
                    --  p_id_usuario
      
             v_resp_bool = gem.f_clon_unicons(v_parametros.id_plantilla ,v_id_uni_cons,p_id_usuario);
          
      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Duplicado desde plantilla  el uni_con  (id_uni_cons'||v_id_uni_cons||') al equipo'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_uni_cons',v_id_uni_cons::varchar);

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
