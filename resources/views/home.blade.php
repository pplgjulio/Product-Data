<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body{
            font-family: "Poppins", sans-serif;
        }

        #add-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        #add-btn:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        #add-btn:focus {
            outline: none;
        }

        .edit-btn, .delete-btn {
            background: none;
            border: none;
            cursor: pointer;
            margin: 0 4px;
            font-size: 16px;
            color: #555;
        }

        .edit-btn:hover {
            color: #3498db;
        }

        .delete-btn:hover {
            color: #e74c3c;
        }
    </style>

</head>
<body>
    @php
    $products = session('products', []);
    @endphp
<table id="myTable" class="display">
  <thead>
    <tr>
      <th>Product Code</th>
      <th>Price</th>
      <th>Quantity</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($products as $index => $product)
    <tr data-index="{{ $index }}">
        <td>{{ $product['product_code'] }}</td>
        <td>{{ $product['price'] }}</td>
        <td>{{ $product['quantity'] }}</td>
        <td>
            <button class="edit-btn"
                    data-index="{{ $index }}"
                    data-code="{{ $product['product_code'] }}"
                    data-price="{{ $product['price'] }}"
                    data-quantity="{{ $product['quantity'] }}">
                <i class="fas fa-pen"></i>
            </button>

            <button class="delete-btn" data-index="{{ $index }}">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
@endforeach
</tbody>
</table>

<button id="add-btn">
  <i class="fas fa-plus"></i>
</button>

<script>
  $(document).ready(function() {
    $('#myTable').DataTable();
  });

  $(document).ready(function () {
    $('#myTable').DataTable();

    $('#myTable').on('click', '.delete-btn', function () {
      const index = $(this).data('index');

      Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the product from the list.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '/delete',
            method: 'POST',
            data: {
              _token: '{{ csrf_token() }}',
              index: index
            },
            success: function (response) {
              Swal.fire('Deleted!', response.message, 'success')
              setTimeout(() => {
                location.reload();
              }, 1000);
            },
            error: function () {
              Swal.fire('Error', 'Failed to delete item.', 'error');
            }
            });
            }
            });
            });
        });
  $(document).ready(function () {
    $('#add-btn').click(function () {
      Swal.fire({
  title: 'Add New Product',
  html:
    `<input id="swal-code" class="swal2-input" placeholder="Product Code">` +
    `<input id="swal-price" type="number" class="swal2-input" placeholder="Price">` +
    `<input id="swal-quantity" type="number" class="swal2-input" placeholder="Quantity">`,
  confirmButtonText: 'Submit',
  showCancelButton: true,
  focusConfirm: false,
  preConfirm: () => {
    const code = $('#swal-code').val();
    const price = $('#swal-price').val();
    const quantity = $('#swal-quantity').val();

    if (!code || !price || !quantity || price <= 0 || quantity <= 0) {
      Swal.showValidationMessage('All fields are required and must be valid.');
      return false;
    }

    return {
      code: code,
      price: price,
      quantity: quantity
    };
  }
}).then((result) => {
  if (result.isConfirmed) {
    const data = result.value;

    $.ajax({
      url: '/submit',
      method: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        product_code: data.code,
        price: data.price,
        quantity: data.quantity
      },
      success: function (response) {
        Swal.fire('Success', 'Product added successfully!', 'success');
        setTimeout(() => {
          location.reload();
        }, 1000);
      },
      error: function () {
        Swal.fire('Error', 'Failed to add product.', 'error');
      }
    });
  }
});

    });
  });
  $(document).ready(function () {
  $('#myTable').DataTable();

  $('.edit-btn').on('click', function () {
    const index = $(this).data('index');
    const code = $(this).data('code');
    const price = $(this).data('price');
    const quantity = $(this).data('quantity');

    Swal.fire({
      title: 'Edit Product',
      html:
        `<input id="edit-code" class="swal2-input" placeholder="Product Code" value="${code}">` +
        `<input id="edit-price" class="swal2-input" type="number" placeholder="Price" value="${price}">` +
        `<input id="edit-quantity" class="swal2-input" type="number" placeholder="Quantity" value="${quantity}">`,
      confirmButtonText: 'Save',
      showCancelButton: true,
      focusConfirm: false,
      preConfirm: () => {
        const newCode = $('#edit-code').val();
        const newPrice = $('#edit-price').val();
        const newQuantity = $('#edit-quantity').val();

        if (!newCode || !newPrice || !newQuantity || newPrice <= 0 || newQuantity <= 0) {
          Swal.showValidationMessage('All fields are required and must be valid.');
          return false;
        }

        return {
          index: index,
          product_code: newCode,
          price: newPrice,
          quantity: newQuantity
        };
      }
    }).then((result) => {
      if (result.isConfirmed) {
        const data = result.value;

        $.ajax({
          url: '/edit',
          method: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            index: data.index,
            product_code: data.product_code,
            price: data.price,
            quantity: data.quantity
          },
          success: function () {
            Swal.fire('Updated', 'Product updated successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
          },
          error: function () {
            Swal.fire('Error', 'Failed to update product.', 'error');
          }
        });
      }
    });
  });
});
</script>

</body>
</html>
