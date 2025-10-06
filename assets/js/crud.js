document.querySelectorAll(".deleteProduct").forEach(btn => {
    btn.addEventListener("click", function() {
        let productId = this.getAttribute("data-id");

        if (confirm("Voulez-vous vraiment supprimer ce produit ?")) {
            fetch("delete_product.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "id=" + productId
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.closest(".col-md-4").remove();
                } else {
                    alert("Erreur : " + data.message);
                }
            })
            .catch(err => console.error("Erreur AJAX :", err));
        }
    });
});
document.querySelectorAll(".editProduct").forEach(btn => {
    btn.addEventListener("click", function() {
        let productId = this.getAttribute("data-id");

        fetch("get_product.php?id=" + productId)
            .then(res => res.json())
            .then(product => {

                document.querySelector("#editProductForm input[name='id']").value = product.id;
                document.querySelector("#editProductForm input[name='name']").value = product.name;
                document.querySelector("#editProductForm input[name='price']").value = product.price;
                document.querySelector("#editProductForm input[name='stock']").value = product.stock;
                document.querySelector("#editProductForm textarea[name='description']").value = product.description;
                document.querySelector("#editProductForm select[name='category_id']").value = product.category_id;
                document.querySelector("#editProductForm input[name='size']").value = product.size;
            })
            .catch(err => console.error("Erreur AJAX :", err));
    });
});
