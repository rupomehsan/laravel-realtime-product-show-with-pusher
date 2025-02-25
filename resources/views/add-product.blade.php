<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    @foreach($products as $product)
        @php
            $product =  (object) $product;
            
        @endphp
        <tr>

      <th scope="col">{{ $product->title }}</th>
      <th scope="col">{{ $product->price }}</th>
      <th scope="col">
        <div>
            <button class="btn btn-info" onclick="editProduct({{ json_encode($product) }})">Edit</button>
            <button class="btn btn-danger">Delete</button>
        </div>
      </th>
    </tr>
        @endforeach
   
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
                  }, 1000);
                    
                }
                
            })
            .catch(error => console.error('Error:', error));

        });


        function editProduct(product) {

            document.getElementById('id').value = product.id;
            document.getElementById('title').value = product.title;
            document.getElementById('price').value = product.price;
            document.getElementById('description').value = product.description;
           
            
        }
          
    </script>

     <script>
        document.addEventListener("DOMContentLoaded", function() {
            Pusher.logToConsole = true;

            var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
                forceTLS: true
            });

            var channel = pusher.subscribe("products");
            channel.bind("product-updated", function(data) {
                let productTable = document.getElementById("product-list"); // Target the table body

                // Check if the product row already exists using a unique identifier
                let existingRow = document.querySelector(`tr[data-product-id='${data.product.id}']`);

                let newRow = `
                    <tr data-product-id="${data.product.id}">
                        <th scope="col">${data.product.title}</th>
                        <th scope="col">${data.product.price}</th>
                        <th scope="col">
                            <div>
                                <button class="btn btn-info" onclick="editProduct(${JSON.stringify(data.product)})">Edit</button>
                                <button class="btn btn-danger">Delete</button>
                            </div>
                        </th>
                    </tr>
                `;

                if (existingRow) {
                    // If product exists, replace the row with updated data
                    existingRow.outerHTML = newRow;
                } else {
                    // If product does not exist, add new row at the beginning of the table
                    productTable.innerHTML = newRow + productTable.innerHTML;
                }
            });
        });
    </script>
</body>
</html>
