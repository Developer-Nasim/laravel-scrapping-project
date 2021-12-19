@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div> 
                <div class="card-body"> 
                    <form action="{{url('link')}}" method="post" class="mb-5">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="url" name="link" placeholder="link" id="" require>
                        <button type="submit">Submit</button>
                    </form>
                    <table class="table mt-5">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Price</th>
                                <th scope="col">Member Price</th>
                                <th scope="col">Created</th>
                            </tr>
                        </thead>
                        <tbody>
 
                            @foreach ($getAll as $item)  
                                <tr data-id='{{$item->id}}'> 
                                    <td>{{$item->urlid}} </td> 
                                    <td>{{$item->price}} </td>
                                    <td></td>
                                    <td>{{$item->created_at}} </td>
                                </tr> 
                            @endforeach 
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
