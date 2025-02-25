<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Lists</title>
    @include('partials.links')
   
</head>
<body>

    <div class="container card my-5">
     <div class="alert alert-success mt-3 d-flex justify-content-between">
         <h5 class="text-center ">Product Lists</h5>
         <a class="btn btn-info float-end" href="{{ route('addProduct')  }}" target="_blank">Add Product</a>
     </div>
    <div class="row align-items-start justify-content-start " id="product-list">
      
       
     
    
    </div>
      </div>
 <script>
        document.addEventListener("DOMContentLoaded", function() {
             fetchLatestProducts()
            Pusher.logToConsole = true;

            var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
                forceTLS: true
            });

            var channel = pusher.subscribe("products");
            channel.bind("product-updated", function(data) {
                fetchLatestProducts()
            });
        });

           function fetchLatestProducts() {
                fetch("{{ route('products.fetch') }}") // Ensure this route returns the latest products
                    .then(response => response.json())
                    .then(data => {
                        let newProduct = document.getElementById("product-list");
                        newProduct.innerHTML = ""; // Clear table before re-rendering
                         data.products.forEach(product => {
                            let newRow = `
                               <div class="col-md-3">
                                    <div class="card m-2" style="width: 15rem;">
                                        <img src="https://placehold.co/600x400/orange/white?text=${product.title}" class="card-img-top" alt="Product Image">
                                        <div class="card-body">
                                            <p class="card-text fw-bold">${product.title}</p>
                                            <p class="card-text fw-bold">${product.description}</p>
                                            <h6 class="fw-bold">$${product.price}</h6>
                                        </div>
                                    </div>
                                </div>
                            `;
                            newProduct.innerHTML += newRow; // Append new row
                        });
                    })
                    .catch(error => console.error("Error fetching products:", error));
        }
    </script>
</body>
</html>
