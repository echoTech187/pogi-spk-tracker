<?php

namespace App\Http\Controllers;

use App\Infrastructure\Persistence\Repositories\EloquentManageRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ManageController extends Controller
{
    public function __construct(
        private readonly EloquentManageRepository $manageRepo
    ) {}

    /**
     * Tampilkan pengelolaan Master Data POGI secara live dari basis data MySQL.
     */
    public function index(Request $request): View
    {
        return view('manage', $this->manageRepo->getAllMasterData());
    }

    /**
     * Simpan data master baru (Dokter, Institusi, Event, Kategori, atau User) melalui Model/Repository.
     */
    public function store(Request $request): JsonResponse
    {
        $entityType = (string) $request->input('entity_type');
        $v = $this->validateStore($request, $entityType);
        if ($v === null) {
            return response()->json(['success' => false, 'message' => 'Tipe entitas tidak valid.'], 422);
        }

        $item = $this->manageRepo->createEntity($entityType, $v, Auth::id());
        return response()->json(['success' => true, 'item' => $item]);
    }

    /**
     * Perbarui data & status akun user (ACTIVE / INACTIVE / BLOCK).
     */
    public function updateUserStatus(Request $request, string $id): JsonResponse
    {
        $v = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'role' => ['nullable', 'string', 'max:50'],
            'account_status' => ['required', 'in:ACTIVE,INACTIVE,BLOCK'],
        ]);

        $accStatus = $this->manageRepo->updateUserStatus($id, $v, Auth::id());
        if ($accStatus === null) {
            return response()->json(['success' => false, 'message' => 'Pengguna tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'account_status' => $accStatus]);
    }

    /**
     * Perbarui entitas master (doctor, institution, event, category, user) melalui Model/Repository.
     */
    public function update(Request $request, string $type, string $id): JsonResponse
    {
        $v = $this->validateUpdate($request, $type, $id);
        if ($v === null) {
            return response()->json(['success' => false, 'message' => 'Tipe entitas tidak valid.'], 422);
        }

        $this->manageRepo->updateEntity($type, $id, $v, Auth::id());
        return response()->json(['success' => true]);
    }

    /**
     * Hapus entitas master dari basis data melalui Model/Repository.
     */
    public function destroy(Request $request, string $type, string $id): JsonResponse
    {
        if (!in_array($type, ['doctor', 'institution', 'event', 'category', 'user'], true)) {
            return response()->json(['success' => false, 'message' => 'Tipe entitas tidak valid.'], 422);
        }

        if ($type === 'user' && (string) Auth::id() === (string) $id) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus akun Anda sendiri yang sedang login.'], 422);
        }

        $this->manageRepo->deleteEntity($type, $id, Auth::id());
        return response()->json(['success' => true]);
    }

    private function validateStore(Request $request, string $entityType): ?array
    {
        return match ($entityType) {
            'doctor' => $request->validate([
                'name' => ['required', 'string', 'max:200'],
                'specialization_title' => ['nullable', 'string', 'max:100'],
                'npa_pogi' => ['nullable', 'string', 'max:50'],
                'nik' => ['nullable', 'string', 'max:64'],
                'str_number' => ['nullable', 'string', 'max:50'],
                'sip_number' => ['nullable', 'string', 'max:50'],
                'sip_expiry_date' => ['nullable', 'date'],
                'email' => ['nullable', 'email', 'max:150'],
                'province' => ['required', 'string', 'max:120'],
                'branch_id' => ['nullable', 'string', 'max:36'],
                'institution_id' => ['nullable', 'string', 'max:36'],
                'institution' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
                'status' => ['nullable', 'in:COMPLETE,INCOMPLETE,PENDING_REVIEW'],
                'period_start' => ['nullable', 'string', 'max:50'],
                'period_end' => ['nullable', 'string', 'max:50'],
                'plataran_sehat_username' => ['nullable', 'string', 'max:150'],
                'plataran_sehat_password' => ['nullable', 'string', 'max:255'],
            ]),
            'institution' => $request->validate([
                'name' => ['required', 'string', 'max:200'],
                'code' => ['nullable', 'string', 'max:50'],
                'institution_type' => ['nullable', 'string', 'max:50'],
                'hospital_class' => ['nullable', 'string', 'max:10'],
                'province' => ['required', 'string', 'max:120'],
                'city' => ['nullable', 'string', 'max:100'],
                'address' => ['nullable', 'string', 'max:500'],
                'status' => ['nullable', 'string', 'in:ACTIVE,INACTIVE,Aktif,Nonaktif,active,inactive'],
                'is_active' => ['nullable'],
            ]),
            'event' => $request->validate([
                'name' => ['required', 'string', 'max:250'],
                'category_id' => ['nullable', 'string', 'max:36'],
                'type' => ['nullable', 'string', 'max:50'],
                'credits' => ['required', 'numeric', 'min:0.5'],
                'organizer' => ['nullable', 'string', 'max:150'],
                'category' => ['nullable', 'string', 'max:120'],
                'date' => ['nullable', 'string', 'max:60'],
                'kemenkes_accreditation_number' => ['nullable', 'string', 'max:100'],
                'plataran_sehat_course_id' => ['nullable', 'string', 'max:100'],
            ]),
            'category' => $request->validate([
                'name' => ['required', 'string', 'max:150'],
                'type' => ['required', 'string', 'max:50'],
                'default_credit_value' => ['nullable', 'numeric', 'min:0'],
                'description' => ['nullable', 'string', 'max:500'],
            ]),
            'user' => $request->validate([
                'name' => ['required', 'string', 'max:150'],
                'email' => ['required', 'email', 'max:150', 'unique:users,email'],
                'phone_number' => ['nullable', 'string', 'max:30'],
                'role' => ['required', 'string', 'max:50'],
                'branch_id' => ['nullable', 'string', 'max:36'],
                'account_status' => ['nullable', 'in:ACTIVE,INACTIVE,BLOCK'],
                'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            ], [
                'email.unique' => 'Email sudah terdaftar pada sistem.',
                'password.required' => 'Password wajib diisi untuk pengguna baru.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
                'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            ]),
            default => null,
        };
    }

    private function validateUpdate(Request $request, string $type, string $id): ?array
    {
        return match ($type) {
            'doctor' => $request->validate([
                'name' => ['required', 'string', 'max:200'],
                'specialization_title' => ['nullable', 'string', 'max:100'],
                'npa_pogi' => ['nullable', 'string', 'max:50'],
                'nik' => ['nullable', 'string', 'max:64'],
                'str_number' => ['nullable', 'string', 'max:50'],
                'sip_number' => ['nullable', 'string', 'max:50'],
                'sip_expiry_date' => ['nullable', 'date'],
                'email' => ['nullable', 'email', 'max:150'],
                'province' => ['required', 'string', 'max:120'],
                'branch_id' => ['nullable', 'string', 'max:36'],
                'institution_id' => ['nullable', 'string', 'max:36'],
                'institution' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
                'status' => ['nullable', 'in:COMPLETE,INCOMPLETE,PENDING_REVIEW'],
                'period_start' => ['nullable', 'string', 'max:50'],
                'period_end' => ['nullable', 'string', 'max:50'],
                'plataran_sehat_username' => ['nullable', 'string', 'max:150'],
                'plataran_sehat_password' => ['nullable', 'string', 'max:255'],
            ]),
            'institution' => $request->validate([
                'name' => ['required', 'string', 'max:200'],
                'code' => ['nullable', 'string', 'max:50'],
                'institution_type' => ['nullable', 'string', 'max:50'],
                'hospital_class' => ['nullable', 'string', 'max:10'],
                'province' => ['required', 'string', 'max:120'],
                'city' => ['nullable', 'string', 'max:100'],
                'address' => ['nullable', 'string', 'max:500'],
                'status' => ['nullable', 'string', 'in:ACTIVE,INACTIVE,Aktif,Nonaktif,active,inactive'],
                'is_active' => ['nullable'],
            ]),
            'event' => $request->validate([
                'name' => ['required', 'string', 'max:250'],
                'category_id' => ['nullable', 'string', 'max:36'],
                'type' => ['nullable', 'string', 'max:50'],
                'credits' => ['required', 'numeric', 'min:0.5'],
                'organizer' => ['nullable', 'string', 'max:150'],
                'category' => ['nullable', 'string', 'max:120'],
                'date' => ['nullable', 'string', 'max:60'],
                'kemenkes_accreditation_number' => ['nullable', 'string', 'max:100'],
                'plataran_sehat_course_id' => ['nullable', 'string', 'max:100'],
            ]),
            'category' => $request->validate([
                'name' => ['required', 'string', 'max:150'],
                'type' => ['required', 'string', 'max:50'],
                'default_credit_value' => ['nullable', 'numeric', 'min:0'],
                'description' => ['nullable', 'string', 'max:500'],
            ]),
            'user' => $request->validate([
                'name' => ['required', 'string', 'max:150'],
                'email' => ['required', 'email', 'max:150', 'unique:users,email,' . $id],
                'phone_number' => ['nullable', 'string', 'max:30'],
                'role' => ['required', 'string', 'max:50'],
                'branch_id' => ['nullable', 'string', 'max:36'],
                'account_status' => ['required', 'in:ACTIVE,INACTIVE,BLOCK'],
                'password' => ['nullable', 'string', 'min:8', 'max:100', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            ], [
                'email.unique' => 'Email sudah terdaftar pada pengguna lain.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
                'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            ]),
            default => null,
        };
    }
}
