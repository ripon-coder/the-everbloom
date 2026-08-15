@extends('admin.layouts.app')

@section('title', 'Message Details - ' . $contact->name)

@section('content')
    <div class="space-y-6">
        <!-- Single Unified Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <a href="{{ route('admin.contacts.index') }}" class="hover:text-gray-900 transition">Contact Messages</a>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-700 font-semibold truncate max-w-[200px]">{{ $contact->name }}</span>
                        </nav>
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Message Details</h1>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                #{{ $contact->id }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Read
                            </span>
                        </div>
                    </div>

                    <!-- Header Action -->
                    <a href="{{ route('admin.contacts.index') }}"
                       class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Messages
                    </a>
                </div>
            </div>

            <!-- Sender Information Grid -->
            <div class="p-5 sm:p-6 border-b border-gray-200 bg-gray-50/50">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <div class="p-3 bg-white border border-gray-200">
                        <span class="text-gray-500 block mb-1">Sender Name</span>
                        <span class="font-semibold text-gray-900">{{ $contact->name }}</span>
                    </div>

                    <div class="p-3 bg-white border border-gray-200">
                        <span class="text-gray-500 block mb-1">Sender Email</span>
                        <a href="mailto:{{ $contact->email }}" class="font-semibold text-blue-600 hover:underline break-all">
                            {{ $contact->email }}
                        </a>
                    </div>

                    <div class="p-3 bg-white border border-gray-200">
                        <span class="text-gray-500 block mb-1">Subject</span>
                        <span class="font-semibold text-gray-900 truncate block">{{ $contact->subject ?: '(No Subject)' }}</span>
                    </div>

                    <div class="p-3 bg-white border border-gray-200">
                        <span class="text-gray-500 block mb-1">Received Time</span>
                        <span class="font-semibold text-gray-900">{{ $contact->created_at ? $contact->created_at->format('M d, Y · H:i') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Message Content -->
            <div class="p-5 sm:p-6 space-y-3">
                <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700">Message Content</h2>
                
                <div class="p-4 bg-gray-50 border border-gray-200 text-xs text-gray-800 whitespace-pre-wrap leading-relaxed">
                    {{ $contact->message }}
                </div>
            </div>

            <!-- Footer Actions (No Delete Button) -->
            <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="text-gray-500">
                    Received {{ $contact->created_at ? $contact->created_at->diffForHumans() : '' }}
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.contacts.index') }}"
                       class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">
                        Back to List
                    </a>
                    <a href="mailto:{{ $contact->email }}?subject=Re: {{ rawurlencode($contact->subject ?: 'Your inquiry') }}" 
                       class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Reply via Email
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection
