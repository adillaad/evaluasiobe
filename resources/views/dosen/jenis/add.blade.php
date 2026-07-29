@extends('dosen.template')
@section('content')

<style>
    li.select2-selection__choice {
        color: #646464;
        font-weight: bolder;
    }

    .tooltipa {
        position: relative;
    }

    .tooltiptext {
    visibility: hidden;
    width: 160px; /* Increased width for better readability */
    background-color: rgba(0, 0, 0, 0.8); /* Use rgba for semi-transparent background */
    color: white;
    text-align: center;
    border-radius: 8px; /* Slightly increased border radius for a softer look */
    padding: 8px; /* Increased padding for better spacing */
    position: absolute;
    z-index: 1;
    bottom: 40%; /* Increased distance from the parent element */
    left: 90%;
    transform: translateX(-50%); /* Center the tooltip horizontally */
    transition: visibility 0.3s ease-in-out; /* Add smooth transition */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .tooltipa:hover .tooltiptext {
        visibility: visible;
    }
</style>

<div class="container-fluid  mb-4">
    <div class="card tooltipa">
        <div class="card-header">
            <div class="fw-bold">
                <h3>Tambah Kriteria Penilaian</h3>
            </div>
        </div>
        <span class="tooltiptext">Format: Contoh ke-1</span>
        <div class="card-body">
            <form action="add-Jenis" method="post">
                @csrf
                <label> Kriteria <span class="text-danger"> *</span></label>
                <input type="text" class="form-control" id="jenis" name="jenis" required>
                <br>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('jenis').addEventListener('input', function() {
    var input = this;
    var words = input.value.split(' ');
    for (var i = 0; i < words.length; i++) {
        words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
    }
    input.value = words.join(' ');
});
</script>
<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>

<script>
//     document.querySelectorAll('#jenis').forEach(function(element) {
//     element.addEventListener('mousedown', function(event) {
//         event.preventDefault();
//     });
// });
</script>
@endsection
