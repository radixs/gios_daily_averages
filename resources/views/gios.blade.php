@extends('layouts.app')
@section('title', 'GIOS Sensor')
@section('content')
    <label for="sensorSelect">Select a sensor:</label>
    <select name="sensorSelect" id="sensorSelect" autocomplete="off">
        @foreach ($sensorCodes as $sensorCode)
            <option  @if ($loop->first) selected="selected" @endif >{{$sensorCode->name}}</option>
        @endforeach
    </select>

    <hr>

    <table id="sensorTable" class="stripe cell-border">

    </table>
@endsection