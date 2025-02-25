<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
   
</head>
<body>

    <div class="container">

    <h5 class="text-center alert alert-success mt-3">Product List</h5>
    <div class="row align-items-start justify-content-start border" id="product-list">
        @foreach($products as $product)
        <div class="col-md-3 ">
            <div class="card m-2" style="width: 15rem; ">
                <img src="https://placehold.co/600x400" class="card-img-top" alt="...">
                <div class="card-body">
                    <p class="card-text">{{ $product->name }}</p>
                    <p class="card-text">{{ $product->description }}</p>
                    <h6>${{ $product->price }}</h6>
                </div>
            </div>
        </div>
        @endforeach
    
    </div>
      </div>
 <script>
        document.addEventListener("DOMContentLoaded", function() {
            Pusher.logToConsole = true;

            var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
                forceTLS: true
            });

            var channel = pusher.subscribe("products");
            channel.bind("product-updated", function(data) {
                let productList = document.getElementById("product-list");
                let newProduct = `
                        <div class="col-md-3">
                            <div class="card m-2" style="width: 15rem;">
                                <img src="https://placehold.co/600x400" class="card-img-top" alt="Product Image">
                                <div class="card-body">
                                    <p class="card-text">${data.product.name}</p>
                                    <p class="card-text">${data.product.description}</p>
                                    <h6>$${data.product.price}</h6>
                                </div>
                            </div>
                        </div>
                    `;
                productList.innerHTML = newProduct + productList.innerHTML;
            });
        });
    </script>
</body>
</html>
