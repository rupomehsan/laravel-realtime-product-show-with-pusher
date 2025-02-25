<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" >
  
</head>
<body>
     
    <div class="container card mt-5 p-3">
        <h5 class="text-center alert alert-success mt-3">Add Product</h5>
        <form id="formSubmit">
            <div class="mb-3">
                <label for="" class="form-label">Name</label>
                <input required type="text" name="name" class="form-control" id="" aria-describedby="emailHelp">
            </div>
            
            <div class="mb-3">
                <label for="" class="form-label">Price</label>
                <input required type="number" name="price" class="form-control" id="">
            </div>
            <div class="mb-3">
                <label for="" class="form-label">description</label>
               <textarea class="form-control" name="description" id="" cols="30" rows="5"></textarea>
               
            </div>
            <button  type="submit" class="btn btn-primary">Submit</button>
        </form>

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
                console.log('Product added:', data.product);
                
            })
            .catch(error => console.error('Error:', error));

        });
          
    </script>
</body>
</html>
