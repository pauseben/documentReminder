<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5 text-center">
        <h2 class="mb-4">
          Dokumentum értesítő
        </h2>
        <form action="{{route('import')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-4">
                <div class="custom-file text-left">
                    <input type="file" name="file" class="custom-file-input" id="customFile" required>
                    <label class="custom-file-label" for="customFile">Fájl</label>
                    @error('file')
                    <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <button class="btn btn-primary">Import</button>
        </form>
        @if($errors->any())
                <div class="alert alert-danger">
                    <p><strong>Hoppá, valami hiba történt</strong></p>
                    <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif
        @if(session('success'))
            <div class="alert alert-success show" role="alert">
                {{session('success')}}
            </div>
        @endif
        <div class="row mt-4">
        <table class="table">
            <thead class="thead-light">
              <tr>
                <th scope="col">TAJ</th>
                <th scope="col">Név</th>
                <th scope="col">E-mail</th>
                <th scope="col">Lejárati idő</th>
                <th scope="col">Megújítás +1 hónapra</th>
              </tr>
            </thead>
            <tbody>  
                @foreach($people as $p)
                    <tr>
                        <th scope="row">{{$p->taj}}</th>
                        <td>{{$p->name}}</td>
                        <td>{{$p->email}}</td>
                        <td>{{$p->expires_at}}</td>
                        <td>
                            <form action="renews/{{$p->id}}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Megújít</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
          </table>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="/cronjob">Manuális emlékeztető e-mail</a>
            </div>
        </div>
    </div>
</body>
</html>