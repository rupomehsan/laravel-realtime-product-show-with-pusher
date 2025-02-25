<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add new product </title>
        @include('partials.links')
  
</head>
<body>
     
   <div class="container card my-5">
    <div id="apiResponse" class="my-2">

    </div>
     <div class="alert alert-success mt-3 d-flex justify-content-between align-items-center">
         <h5 class="text-center ">Add Product</h5>
         <a class="btn btn-info float-end" href="{{ route('getAddProduct')  }}" target="_blank">All Product</a>
     </div>

        <form id="formSubmit">
            <input type="hidden"  name="id" id="id">
            <div class="mb-3">
                <label for="" class="form-label">Title</label>
                <input  required type="text" name="title" class="form-control" id="title">
            </div>
            
            <div class="mb-3">
                <label for="" class="form-label">Price</label>
                <input required type="text" name="price" class="form-control" id="price">
            </div>
            <div class="mb-3">
                <label for="" class="form-label">description</label>
               <textarea class="form-control" name="description" id="description" cols="30"  rows="5"></textarea>
               
            </div>
            <button  type="submit" class="btn btn-primary my-3">Submit</button>
        </form>
<hr>
        
    <div class="card my-2">
        <table class="table table-striped table-hover table-bordered">
  <thead>
   
  </thead>
  <tbody id="product-list">


   
   
  </tbody>
</table>
    </div>

    </div>


    <script>
         document.getElementById("formSubmit").addEventListener("submit", function(e) {
       
            e.preventDefault();
        
            let form = new FormData(this);
            form.append('_token', '{{ csrf_token() }}'); // Ensure CSRF token is included
            fetch('/add-product', {
                method: 'POST',
                body: form,
            })
            .then(response => response.json())
            .then(data => {

                if (data.product) {
                    document.getElementById('formSubmit').reset();
                     document.getElementById('apiResponse').classList.remove('d-none')
                    document.getElementById('apiResponse').innerHTML = `
                    <div class="alert alert-success">
                        Product added successfully! 
                    </div>
                `;


                  setTimeout(() => {
                     document.getElementById('apiResponse').classList.add('d-none')
                     document.getElementById('id').value = ''
                  }, 1000);
                    
                }
                
            })
            .catch(error => console.error('Error:', error));

        });


       
          
    </script>

     <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetchLatestProducts();
            Pusher.logToConsole = true;

            var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
                forceTLS: true
            });

            var channel = pusher.subscribe("products");
            channel.bind("product-updated", function () {
                fetchLatestProducts(); 
            });
        });

        function fetchLatestProducts() {
                fetch("{{ route('products.fetch') }}") // Ensure this route returns the latest products
                    .then(response => response.json())
                    .then(data => {
                        let productTable = document.getElementById("product-list");
                        productTable.innerHTML = ""; // Clear table before re-rendering



                        data.products.forEach(product => {
                            let newRow = `
                                <tr data-product-id="${product.id}">
                                    <th scope="col">${product.title}</th>
                                    <th scope="col">${product.price}</th>
                                    <th scope="col">
                                        <div>
                                            <button class="btn btn-info" onclick='editProduct(${JSON.stringify(product)})'>Edit</button>
                                            <button class="btn btn-danger" onclick='deleteProduct(${JSON.stringify(product.id)})'>Delete</button>
                                        </div>
                                    </th>
                                </tr>
                            `;
                            productTable.innerHTML += newRow; // Append new row
                        });
                    })
                    .catch(error => console.error("Error fetching products:", error));
        }

        // Edit Product Function
        function editProduct(product) {
            console.log("Editing product:", product); // Debugging

            // Ensure product data is correct before setting values
            if (!product || !product.id) {
                console.error("Invalid product data:", product);
                return;
            }

            document.getElementById('id').value = product.id;
            document.getElementById('title').value = product.title;
            document.getElementById('price').value = product.price;
            document.getElementById('description').value = product.description;
        }

        function deleteProduct(id) {
            if (!confirm("Are you sure you want to delete this product?")) {
                return; // Stop if user cancels
            }

            fetch(`{{ route('products.delete', ':id') }}`.replace(':id', id), {
                method: "POST",
                headers: {
                   "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('apiResponse').classList.remove('d-none')
                    document.getElementById('apiResponse').innerHTML = `
                    <div class="alert alert-success">
                        Product deleted successfully! 
                    </div>
                    `
                    fetchLatestProducts(); 
                    setTimeout(() => {
                        document.getElementById('apiResponse').classList.add('d-none')
                     }, 1000);

                } else {
                    alert("Failed to delete product.");
                }
            })
            .catch(error => console.error("Error deleting product:", error));
        }
    </script>
</body>
</html>
