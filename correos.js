let principal = document.querySelector("main")
let navegacion = document.querySelector("ul")
var supercorreos;
fetch("back.php")
  .then(function(response) {
    return response.text();  // Cambia a .text() para ver la respuesta raw
  })
  .then(function(text) {
    console.log(text);  // Imprime el contenido para ver qué está devolviendo el servidor
    return JSON.parse(text);  // Intenta analizarlo manualmente
  })
  .then(function(datos) {
    console.log(datos);
    // Continuar con tu lógica
  })
  .catch(function(error) {
    console.error("Hubo un error con la petición fetch:", error);
  })
	.then(function(datos){
		console.log(datos)
		let clientes = datos.unique_contacts
		clientes.forEach(function(cliente){
			let elementolista = document.createElement("li")
			elementolista.textContent = cliente
			elementolista.onclick = function(){
				console.log("click en elemento lista")
				filtrar(cliente)
			}
			navegacion.appendChild(elementolista)
			let seccion = document.createElement("section")
			let titulo = document.createElement("h3")
			titulo.textContent = cliente
			seccion.appendChild(titulo)
			principal.appendChild(seccion)
			let correos = datos.emails.sort((a, b) => new Date(a.fecha) - new Date(b.fecha));
			supercorreos = correos
			correos.forEach(function(email){
				if(email.from == cliente){
					let articulo = document.createElement("article")
					articulo.innerHTML = "<span>"+email.fecha+"</span><span>"+email.from+"</span><span>➡️ </span><span>"+email.to+"</span><span>"+email.asunto+"</span>";
					seccion.appendChild(articulo)
				}
				if(email.to == cliente){
					let articulo = document.createElement("article")
					articulo.innerHTML = "<span>"+email.fecha+"</span><span>"+email.to+"</span><span>⬅️ </span><span>"+email.from+"</span><span>"+email.asunto+"</span>";
					seccion.appendChild(articulo)
				}
			})
		})
	})
	
	function filtrar(cliente){
		let seccion = document.createElement("section")
		principal.innerHTML = ""
		supercorreos.forEach(function(email){
			if(email.from == cliente){
				let articulo = document.createElement("article")
				articulo.innerHTML = "<span>"+email.fecha+"</span><span>"+email.from+"</span><span>➡️ </span><span>"+email.to+"</span><span>"+email.asunto+"</span>";
				seccion.appendChild(articulo)
			}
			if(email.to == cliente){
				let articulo = document.createElement("article")
				articulo.innerHTML = "<span>"+email.fecha+"</span><span>"+email.to+"</span><span>⬅️ </span><span>"+email.from+"</span><span>"+email.asunto+"</span>";
				seccion.appendChild(articulo)
			}
		})
		principal.appendChild(seccion)
	}
