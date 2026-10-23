<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="document_type" class="block text-sm font-semibold text-slate-700 mb-1">Tipo de Documento *</label>
        <select id="document_type" name="document_type" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm" required>
            @foreach (['CC' => 'Cédula de Ciudadanía (CC)', 'NIT' => 'NIT / Empresa', 'CE' => 'Cédula de Extranjería (CE)', 'PP' => 'Pasaporte (PP)'] as $value => $label)
                <option value="{{ $value }}" @selected(old('document_type', $client->document_type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('document_type')
            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="document_number" class="block text-sm font-semibold text-slate-700 mb-1">Número de Documento *</label>
        <input id="document_number" name="document_number" type="text"
               value="{{ old('document_number', $client->document_number) }}"
               placeholder="Ej: 1088123456"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm" required>
        @error('document_number')
            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-4">
    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nombre Completo o Razón Social *</label>
    <input id="name" name="name" type="text"
           value="{{ old('name', $client->name) }}"
           placeholder="Ej: Juan Pérez o Inversiones SAS"
           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm" required>
    @error('name')
        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
    <div>
        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Correo Electrónico (Opcional)</label>
        <input id="email" name="email" type="email"
               value="{{ old('email', $client->email) }}"
               placeholder="cliente@ejemplo.com"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm">
        @error('email')
            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1">Teléfono / Celular (Opcional)</label>
        <input id="phone" name="phone" type="text"
               value="{{ old('phone', $client->phone) }}"
               placeholder="Ej: 3001234567"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm">
        @error('phone')
            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-4">
    <label for="address" class="block text-sm font-semibold text-slate-700 mb-1">Dirección de Entrega / Domicilio</label>
    <input id="address" name="address" type="text"
           value="{{ old('address', $client->address) }}"
           placeholder="Ej: Calle 10 # 20-30 Barrio Central"
           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm">
    @error('address')
        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mt-4 pt-2">
    <label class="inline-flex items-center space-x-2 cursor-pointer">
        <input type="checkbox" name="active" value="1"
               class="w-4 h-4 rounded text-orange-600 border-slate-300 focus:ring-orange-500"
               @checked(old('active', $client->active))>
        <span class="text-sm font-medium text-slate-700">Cliente Activo para Facturación</span>
    </label>
</div>
