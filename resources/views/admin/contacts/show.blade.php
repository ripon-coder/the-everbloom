@extends('admin.layouts.app')

@section('title', 'Message Details')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold text-gray-800">Message from {{ $contact->name }}</h1>
        <a href="{{ route('admin.contacts.index') }}" class="text-sm text-gray-600 hover:underline">Back to List</a>
    </div>

    <div class="bg-white border border-gray-200 rounded shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 pb-6 border-b">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Sender Name</p>
                <p class="text-gray-900 font-medium">{{ $contact->name }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Sender Email</p>
                <a href="mailto:{{ $contact->email }}" class="text-blue-600 hover:underline">{{ $contact->email }}</a>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Subject</p>
                <p class="text-gray-900 font-medium">{{ $contact->subject ?: 'No Subject' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Received Date</p>
                <p class="text-gray-900 font-medium">{{ $contact->created_at->format('M d, Y - h:i A') }} ({{ $contact->created_at->diffForHumans() }})</p>
            </div>
        </div>

        <div class="mb-8">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Message Content</p>
            <div class="bg-gray-50 rounded p-4 text-gray-800 whitespace-pre-wrap leading-relaxed">
                {{ $contact->message }}
            </div>
        </div>

        <div class="flex items-center gap-4">
            <a href="mailto:{{ $contact->email }}?subject=Re: {{ $contact->subject }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded transition">
                Reply via Email
            </a>
            <button onclick="showDeleteModal('message', '{{ route('admin.contacts.destroy', $contact->id) }}', '{{ $contact->name }}')" 
                    class="text-red-600 hover:underline font-medium">
                Delete Message
            </button>
        </div>
    </div>
</div>
@endsection
