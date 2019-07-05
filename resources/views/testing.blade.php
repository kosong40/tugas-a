<form action="{{url('testing/upload')}}" method="post" enctype="multipart/form-data">
@csrf
<input type="file" name="image" id="">
@if($errors->get('image'))
@foreach ($errors->get('image') as $pesan)
    <div class="invalid-feedback">
        {{$pesan}}
    </div>
@endforeach
@endif
<button type="submit">Simpan</button>
</form>