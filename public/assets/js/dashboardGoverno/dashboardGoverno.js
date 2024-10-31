
        function showSection(sectionId) {
            document.querySelectorAll('.card').forEach(card => card.classList.add('hidden-section'));
            document.getElementById(sectionId).classList.remove('hidden-section');
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            document.querySelector(`.nav-link[href="#${sectionId}"]`).classList.add('active');
        }

        function addBenefit() {
            const tipo = document.getElementById("tipo-beneficio").value;
            const valor = document.getElementById("valor-beneficio").value;
            const destinatario = document.getElementById("destinatario-beneficio").value;

            const benefitItem = document.createElement("div");
            benefitItem.className = "list-group-item d-flex justify-content-between align-items-center";
            benefitItem.innerHTML = `${tipo} - R$ ${valor} (${destinatario})
                <button class="btn btn-sm btn-danger" onclick="removeItem(this)">Excluir</button>`;
            document.getElementById("benefitsList").appendChild(benefitItem);
        }

        function addLegislation() {
            const tipo = document.getElementById("tipo-legislacao").value;
            const descricao = document.getElementById("texto-legislacao").value;

            const legislationItem = document.createElement("div");
            legislationItem.className = "list-group-item d-flex justify-content-between align-items-center";
            legislationItem.innerHTML = `${tipo} - ${descricao}
                <button class="btn btn-sm btn-danger" onclick="removeItem(this)">Excluir</button>`;
            document.getElementById("legislationList").appendChild(legislationItem);
        }

        
        function addTax() {
            const destinatario = document.getElementById("destinatario-taxa").value;
            const nome = document.getElementById("nome-taxa").value;
            const preco = document.getElementById("preco-taxa").value;
            const descricao = document.getElementById("descricao-taxa").value;

            const taxItem = document.createElement("div");
            taxItem.className = "list-group-item d-flex justify-content-between align-items-center";
            taxItem.innerHTML = `${destinatario} - ${nome}: R$ ${preco}<br><small>${descricao}</small>
                <button class="btn btn-sm btn-danger" onclick="removeItem(this)">Excluir</button>`;
            document.getElementById("taxList").appendChild(taxItem);
        }

        function removeItem(button) {
            button.closest(".list-group-item").remove();
        }
