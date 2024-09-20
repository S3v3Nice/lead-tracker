<?php

namespace App\Http\Controllers;

use App\Models\Enums\LeadStatusType;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Rules\ColumnExistsRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    use ApiJsonResponseTrait;

    const RUSSIAN_PHONE_REGEX = '/^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/';

    public function get(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page' => ['integer'],
            'per_page' => ['integer'],
            'sort_field' => ['string', new ColumnExistsRule(Lead::getModel()->getTable())],
            'sort_order' => ['integer', 'min:-1', 'max:1'],
        ]);

        if ($validator->fails()) {
            return $this->errorJsonResponse('', $validator->errors());
        }

        $sortOrder = $request->integer('sort_order', -1);

        if ($sortOrder === 0) {
            $sortField = 'created_at';
            $sortDirection = 'desc';
        } else {
            $sortField = $request->string('sort_field', 'created_at');
            $sortDirection = $sortOrder === -1 ? 'desc' : 'asc';
        }

        $query = Lead::orderBy($sortField, $sortDirection);

        $statusCounts = LeadStatus::select(['type', DB::raw('COUNT(*) as count')])
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function (LeadStatus $leadStatus) {
                return [$leadStatus->type->value => $leadStatus->count];
            });
        $statusCounts = array_merge([
            LeadStatusType::New->value => 0,
            LeadStatusType::Pending->value => 0,
            LeadStatusType::Done->value => 0,
        ], $statusCounts->toArray());

        if ($request->has('per_page') || $request->has('page')) {
            $perPage = $request->integer('per_page', 10);
            $leads = $query->paginate($perPage);

            return $this->successJsonResponse([
                'records' => $leads->items(),
                'status_counts' => $statusCounts,
                'pagination' => [
                    'current_page' => $leads->currentPage(),
                    'total_pages' => $leads->lastPage(),
                ],
            ]);
        }

        return $this->successJsonResponse([
            'records' => $query->get(),
            'status_counts' => $statusCounts,
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:' . self::RUSSIAN_PHONE_REGEX],
            'email' => ['required', 'email', 'max:255'],
            'appeal' => ['required', 'string', 'max:255', 'nullable'],
        ]);

        if ($validator->fails()) {
            return $this->errorJsonResponse('', $validator->errors());
        }

        $lead = Lead::create($request->only(['first_name', 'last_name', 'phone', 'email', 'appeal']));

        $leadStatus = LeadStatus::make();
        $leadStatus->type = LeadStatusType::New;
        $leadStatus->lead()->associate($lead);
        $leadStatus->save();

        return $this->successJsonResponse();
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $lead = Lead::find($id);
        if ($lead === null) {
            return $this->errorJsonResponse("Лид с id $id не найден.");
        }

        $validator = Validator::make($request->all(), [
            'first_name' => ['string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'phone' => ['regex:' . self::RUSSIAN_PHONE_REGEX],
            'email' => ['email', 'max:255'],
            'appeal' => ['string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->errorJsonResponse('', $validator->errors());
        }

        $lead->update($request->only(['first_name', 'last_name', 'phone', 'email', 'appeal']));

        return $this->successJsonResponse();
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $lead = Lead::find($id);
        if ($lead === null) {
            return $this->errorJsonResponse("Лид с id $id не найден.");
        }

        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::enum(LeadStatusType::class)],
        ]);

        if ($validator->fails()) {
            return $this->errorJsonResponse('', $validator->errors());
        }

        $leadStatus = $lead->status;
        $leadStatus->type = $request->get('status');
        $leadStatus->save();

        return $this->successJsonResponse();
    }

    public function delete(int $id): JsonResponse
    {
        $lead = Lead::find($id);
        if ($lead === null) {
            return $this->errorJsonResponse("Лид с id $id не найден.");
        }

        $lead->delete();
        return $this->successJsonResponse();
    }
}
