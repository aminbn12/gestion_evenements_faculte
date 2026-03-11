<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Room;
use App\Models\Professor;
use App\Models\Resident;
use App\Models\ExamAssignment;
use App\Models\Absence;
use App\Exports\ExamsExport;
use App\Exports\ExamsTemplateExport;
use App\Imports\ExamsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ExamController extends Controller
{
    /**
     * Display exam surveillance dashboard.
     */
    public function index()
    {
        $exams = Exam::with(['assignments.room', 'assignments.professors', 'assignments.residents'])
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();
        
        $rooms = Room::all();
        $professors = Professor::with('user')->get();
        $residents = Resident::with('user')->get();
        
        // Get unique subjects from exams for the dropdown
        $subjects = Exam::distinct()->pluck('subject')->filter()->sort()->values();
        
        return view('exams.index', compact('exams', 'rooms', 'professors', 'residents', 'subjects'));
    }

    /**
     * Display calendar view.
     */
    public function calendar()
    {
        $exams = Exam::with(['assignments.room', 'assignments.professors', 'assignments.residents'])
            ->get();
        
        return view('exams.calendar', compact('exams'));
    }

    /**
     * Display planning (assignments) view.
     */
    public function planning()
    {
        $assignments = ExamAssignment::with(['exam', 'room', 'professors', 'residents'])
            ->get();
        
        $exams = Exam::orderBy('date', 'desc')->get();
        $rooms = Room::all();
        $professors = Professor::all();
        $residents = Resident::all();
        
        return view('exams.planning', compact('assignments', 'exams', 'rooms', 'professors', 'residents'));
    }

    /**
     * Display stats/equity view.
     */
    public function stats()
    {
        $professors = Professor::with(['user', 'assignments.exam'])->get();
        $residents = Resident::with(['user', 'assignments.exam'])->get();
        
        return view('exams.stats', compact('professors', 'residents'));
    }

    /**
     * Store a new exam.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|integer|min:1',
            'promo' => 'required|string',
            'subject' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Exam::create($request->all());

        return redirect()->route('exams.index')
            ->with('success', 'Examen créé avec succès!');
    }

    /**
     * Update an exam.
     */
    public function update(Request $request, Exam $exam)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|integer|min:1',
            'promo' => 'required|string',
            'subject' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $exam->update($request->all());

        return redirect()->route('exams.index')
            ->with('success', 'Examen mis à jour avec succès!');
    }

    /**
     * Delete an exam.
     */
    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('exams.index')
            ->with('success', 'Examen supprimé avec succès!');
    }

    /**
     * Store a new room.
     */
    public function storeRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:rooms,name',
            'prof_capacity' => 'required|integer|min:1',
            'resident_capacity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Room::create($request->all());

        return redirect()->route('exams.index')
            ->with('success', 'Salle créée avec succès!');
    }

    /**
     * Store a new professor.
     */
    public function storeProfessor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'rank' => 'required|in:Pr,Dr',
            'responsible_promo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Professor::create($request->all());

        return redirect()->route('exams.index')
            ->with('success', 'Enseignant créé avec succès!');
    }

    /**
     * Store a new resident.
     */
    public function storeResident(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'level' => 'required|integer|min:1|max:4',
            'specialty' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Resident::create($request->all());

        return redirect()->route('exams.index')
            ->with('success', 'Résident créé avec succès!');
    }

    /**
     * Create an assignment.
     */
    public function storeAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|exists:exams,id',
            'room_id' => 'required|exists:rooms,id',
            'prof_ids' => 'array',
            'prof_ids.*' => 'exists:professors,id',
            'resident_ids' => 'array',
            'resident_ids.*' => 'exists:residents,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if assignment already exists
        $existing = ExamAssignment::where('exam_id', $request->exam_id)
            ->where('room_id', $request->room_id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'Cette affectation existe déjà!')
                ->withInput();
        }

        $assignment = ExamAssignment::create([
            'exam_id' => $request->exam_id,
            'room_id' => $request->room_id,
        ]);

        if ($request->prof_ids) {
            $assignment->professors()->attach($request->prof_ids);
        }

        if ($request->resident_ids) {
            $assignment->residents()->attach($request->resident_ids);
        }

        return redirect()->route('exams.planning')
            ->with('success', 'Affectation créée avec succès!');
    }

    /**
     * Delete an assignment.
     */
    public function destroyAssignment(ExamAssignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('exams.planning')
            ->with('success', 'Affectation supprimée avec succès!');
    }

    /**
     * Store an absence.
     */
    public function storeAbsence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'professor_id' => 'required_without:resident_id|nullable|exists:professors,id',
            'resident_id' => 'required_without:professor_id|nullable|exists:residents,id',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Absence::create($request->all());

        return redirect()->route('exams.stats')
            ->with('success', 'Absence enregistrée avec succès!');
    }

    /**
     * Delete an absence.
     */
    public function destroyAbsence(Absence $absence)
    {
        $absence->delete();

        return redirect()->route('exams.stats')
            ->with('success', 'Absence supprimée avec succès!');
    }

    /**
     * Export exams to Excel.
     */
    public function exportExams()
    {
        return Excel::download(new ExamsExport, 'examens_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Import exams from Excel.
     */
    public function importExams(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Excel::import(new ExamsImport, $request->file('file'));

            return redirect()->route('exams.index')
                ->with('success', 'Examens importés avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download exam template.
     */
    public function downloadExamTemplate()
    {
        return Excel::download(new ExamsTemplateExport, 'modele_examens.xlsx');
    }
}
