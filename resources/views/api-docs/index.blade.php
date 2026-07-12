@php use Illuminate\Support\Str;
    $baseUrl = url('/api');
    $grouped = [
        'auth_group' => [[
            'method' => 'POST', 'endpoint' => '/api/auth/login', 'desc_key' => 'auth_login_desc', 'ability' => 'none',
            'params' => [['email','string','User email address',true],['password','string','User password',true]],
        ],[
            'method' => 'POST', 'endpoint' => '/api/auth/register', 'desc_key' => 'auth_register_desc', 'ability' => 'none',
            'params' => [['name','string','User full name',true],['email','string','Email (must be unique)',true],['password','string','Min 8 characters',true],['password_confirmation','string','Must match password',true]],
        ],[
            'method' => 'GET', 'endpoint' => '/api/auth/user', 'desc_key' => 'auth_user_desc', 'ability' => '*',
        ],[
            'method' => 'POST', 'endpoint' => '/api/auth/logout', 'desc_key' => 'auth_logout_desc', 'ability' => '*',
        ]],
        'workspace_group' => [[
            'method' => 'GET', 'endpoint' => '/api/workspaces', 'desc_key' => 'workspace_list_desc', 'ability' => 'workspace:read',
        ],[
            'method' => 'POST', 'endpoint' => '/api/workspaces', 'desc_key' => 'Create a new workspace', 'ability' => 'workspace:write',
            'params' => [['name','string','Workspace name',true],['currency','string','Currency code (USD, EUR, etc)',false]],
        ],[
            'method' => 'GET', 'endpoint' => '/api/workspaces/{id}', 'desc_key' => 'workspace_current_desc', 'ability' => 'workspace:read',
        ],[
            'method' => 'PUT', 'endpoint' => '/api/workspaces/{id}', 'desc_key' => 'Update workspace settings', 'ability' => 'workspace:write',
        ],[
            'method' => 'DELETE', 'endpoint' => '/api/workspaces/{id}', 'desc_key' => 'Delete a workspace', 'ability' => 'workspace:write',
        ],[
            'method' => 'POST', 'endpoint' => '/api/workspaces/{id}/switch', 'desc_key' => 'workspace_switch_desc', 'ability' => 'workspace:write',
        ]],
        'incomes_group' => [[
            'method' => 'GET', 'endpoint' => '/api/workspace/incomes', 'desc_key' => 'incomes_list_desc', 'ability' => 'income:read',
        ],[
            'method' => 'POST', 'endpoint' => '/api/workspace/incomes', 'desc_key' => 'incomes_create_desc', 'ability' => 'income:write',
            'params' => [['amount','number','Income amount (positive)',true],['description','string','Description',false],['category_id','integer','Income category ID',true],['date','string (date)','Date (Y-m-d)',true],['is_recurring','boolean','Recurring?',false],['recurring_frequency','string','daily/weekly/monthly/yearly',false]],
        ],[
            'method' => 'GET', 'endpoint' => '/api/workspace/incomes/{id}', 'desc_key' => 'incomes_show_desc', 'ability' => 'income:read',
        ],[
            'method' => 'PUT', 'endpoint' => '/api/workspace/incomes/{id}', 'desc_key' => 'incomes_update_desc', 'ability' => 'income:write',
        ],[
            'method' => 'DELETE', 'endpoint' => '/api/workspace/incomes/{id}', 'desc_key' => 'incomes_delete_desc', 'ability' => 'income:write',
        ]],
        'expenses_group' => [[
            'method' => 'GET', 'endpoint' => '/api/workspace/expenses', 'desc_key' => 'expenses_list_desc', 'ability' => 'expense:read',
        ],[
            'method' => 'POST', 'endpoint' => '/api/workspace/expenses', 'desc_key' => 'expenses_create_desc', 'ability' => 'expense:write',
            'params' => [['amount','number','Expense amount (positive)',true],['description','string','Description',false],['category_id','integer','Expense category ID',true],['date','string (date)','Date (Y-m-d)',true]],
        ],[
            'method' => 'GET', 'endpoint' => '/api/workspace/expenses/{id}', 'desc_key' => 'expenses_show_desc', 'ability' => 'expense:read',
        ],[
            'method' => 'PUT', 'endpoint' => '/api/workspace/expenses/{id}', 'desc_key' => 'expenses_update_desc', 'ability' => 'expense:write',
        ],[
            'method' => 'DELETE', 'endpoint' => '/api/workspace/expenses/{id}', 'desc_key' => 'expenses_delete_desc', 'ability' => 'expense:write',
        ]],
        'debts_group' => [[
            'method' => 'GET', 'endpoint' => '/api/workspace/debts', 'desc_key' => 'debts_list_desc', 'ability' => 'debt:read',
        ],[
            'method' => 'POST', 'endpoint' => '/api/workspace/debts', 'desc_key' => 'debts_create_desc', 'ability' => 'debt:write',
            'params' => [['amount','number','Debt amount',true],['description','string','Description',true],['due_date','string (date)','Due date (Y-m-d)',true],['type','string','personal/mortgage/student/car/medical/other',false]],
        ],[
            'method' => 'GET', 'endpoint' => '/api/workspace/debts/{id}', 'desc_key' => 'debts_show_desc', 'ability' => 'debt:read',
        ],[
            'method' => 'PUT', 'endpoint' => '/api/workspace/debts/{id}', 'desc_key' => 'debts_update_desc', 'ability' => 'debt:write',
        ],[
            'method' => 'DELETE', 'endpoint' => '/api/workspace/debts/{id}', 'desc_key' => 'debts_delete_desc', 'ability' => 'debt:write',
        ]],
        'assets_group' => [[
            'method' => 'GET', 'endpoint' => '/api/workspace/assets', 'desc_key' => 'assets_list_desc', 'ability' => 'asset:read',
        ],[
            'method' => 'POST', 'endpoint' => '/api/workspace/assets', 'desc_key' => 'assets_create_desc', 'ability' => 'asset:write',
            'params' => [['name','string','Asset name',true],['value','number','Asset value',true],['type','string','bank/cash/property/vehicle/investment/other',false]],
        ],[
            'method' => 'GET', 'endpoint' => '/api/workspace/assets/{id}', 'desc_key' => 'assets_show_desc', 'ability' => 'asset:read',
        ],[
            'method' => 'PUT', 'endpoint' => '/api/workspace/assets/{id}', 'desc_key' => 'assets_update_desc', 'ability' => 'asset:write',
        ],[
            'method' => 'DELETE', 'endpoint' => '/api/workspace/assets/{id}', 'desc_key' => 'assets_delete_desc', 'ability' => 'asset:write',
        ]],
        'budgets_group' => [[
            'method' => 'GET', 'endpoint' => '/api/workspace/budgets', 'desc_key' => 'budgets_list_desc', 'ability' => 'budget:read',
        ],[
            'method' => 'POST', 'endpoint' => '/api/workspace/budgets', 'desc_key' => 'budgets_create_desc', 'ability' => 'budget:write',
            'params' => [['name','string','Budget name',true],['amount','number','Budget amount',true],['period','string','monthly/yearly/weekly',true],['category_id','integer','Category ID',false]],
        ],[
            'method' => 'GET', 'endpoint' => '/api/workspace/budgets/{id}', 'desc_key' => 'budgets_show_desc', 'ability' => 'budget:read',
        ],[
            'method' => 'PUT', 'endpoint' => '/api/workspace/budgets/{id}', 'desc_key' => 'budgets_update_desc', 'ability' => 'budget:write',
        ],[
            'method' => 'DELETE', 'endpoint' => '/api/workspace/budgets/{id}', 'desc_key' => 'budgets_delete_desc', 'ability' => 'budget:write',
        ]],
        'goals_group' => [[
            'method' => 'GET', 'endpoint' => '/api/workspace/goals', 'desc_key' => 'goals_list_desc', 'ability' => 'goal:read',
        ],[
            'method' => 'POST', 'endpoint' => '/api/workspace/goals', 'desc_key' => 'goals_create_desc', 'ability' => 'goal:write',
            'params' => [['name','string','Goal name',true],['target_amount','number','Target amount',true],['deadline','string (date)','Target date (Y-m-d)',false]],
        ],[
            'method' => 'GET', 'endpoint' => '/api/workspace/goals/{id}', 'desc_key' => 'goals_show_desc', 'ability' => 'goal:read',
        ],[
            'method' => 'PUT', 'endpoint' => '/api/workspace/goals/{id}', 'desc_key' => 'goals_update_desc', 'ability' => 'goal:write',
        ],[
            'method' => 'DELETE', 'endpoint' => '/api/workspace/goals/{id}', 'desc_key' => 'goals_delete_desc', 'ability' => 'goal:write',
        ]],
        'transactions_group' => [[
            'method' => 'GET', 'endpoint' => '/api/workspace/transactions', 'desc_key' => 'transactions_list_desc', 'ability' => 'transaction:read',
        ],[
            'method' => 'GET', 'endpoint' => '/api/workspace/dashboard', 'desc_key' => 'View dashboard KPIs', 'ability' => 'dashboard:read',
        ]],
        'categories_group' => [[
            'method' => 'GET', 'endpoint' => '/api/workspace/income-categories', 'desc_key' => 'List income categories', 'ability' => 'income-categories:read',
        ],[
            'method' => 'POST', 'endpoint' => '/api/workspace/income-categories', 'desc_key' => 'Create income category', 'ability' => 'income-categories:write',
            'params' => [['name','string','Category name',true],['color','string','Hex color code',false]],
        ],[
            'method' => 'PUT', 'endpoint' => '/api/workspace/income-categories/{id}', 'desc_key' => 'Update income category', 'ability' => 'income-categories:write',
        ],[
            'method' => 'DELETE', 'endpoint' => '/api/workspace/income-categories/{id}', 'desc_key' => 'Delete income category', 'ability' => 'income-categories:write',
        ],[
            'method' => 'GET', 'endpoint' => '/api/workspace/expense-categories', 'desc_key' => 'List expense categories', 'ability' => 'expense-categories:read',
        ],[
            'method' => 'POST', 'endpoint' => '/api/workspace/expense-categories', 'desc_key' => 'Create expense category', 'ability' => 'expense-categories:write',
        ],[
            'method' => 'PUT', 'endpoint' => '/api/workspace/expense-categories/{id}', 'desc_key' => 'Update expense category', 'ability' => 'expense-categories:write',
        ],[
            'method' => 'DELETE', 'endpoint' => '/api/workspace/expense-categories/{id}', 'desc_key' => 'Delete expense category', 'ability' => 'expense-categories:write',
        ]],
        'subscription_group' => [[
            'method' => 'GET', 'endpoint' => '/api/plans', 'desc_key' => 'List all subscription plans', 'ability' => 'subscription:read',
        ],[
            'method' => 'GET', 'endpoint' => '/api/workspace/subscription', 'desc_key' => 'Get current subscription', 'ability' => 'subscription:read',
        ],[
            'method' => 'POST', 'endpoint' => '/api/workspace/subscription/change-plan', 'desc_key' => 'Change subscription plan', 'ability' => 'subscription:write',
        ],[
            'method' => 'POST', 'endpoint' => '/api/workspace/subscription/cancel', 'desc_key' => 'Cancel subscription', 'ability' => 'subscription:write',
        ],[
            'method' => 'POST', 'endpoint' => '/api/coupon/validate', 'desc_key' => 'Validate a coupon code', 'ability' => 'subscription:read',
        ]],
        'other_group' => [[
            'method' => 'GET', 'endpoint' => '/api/health', 'desc_key' => 'Check API health status', 'ability' => 'none',
        ]],
    ];
    $groupIcons = [
        'auth_group' => 'bi-shield-lock',
        'workspace_group' => 'bi-layers',
        'incomes_group' => 'bi-graph-up-arrow',
        'expenses_group' => 'bi-graph-down-arrow',
        'debts_group' => 'bi-credit-card-2-front',
        'assets_group' => 'bi-building',
        'budgets_group' => 'bi-pie-chart',
        'goals_group' => 'bi-bullseye',
        'transactions_group' => 'bi-arrow-left-right',
        'categories_group' => 'bi-tags',
        'subscription_group' => 'bi-star',
        'other_group' => 'bi-gear',
    ];
@endphp

<x-guest-layout>
    <x-slot:title>{{ __('api-docs.page_title') }} - {{ config('app.name') }}</x-slot>

    <div class="api-docs">
        <div class="container">
            <div class="api-docs-header">
                <h1>{{ __('api-docs.page_title') }}</h1>
                <p>{{ __('api-docs.page_description') }}</p>
            </div>

            <div class="row">
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="position-sticky" style="top:2rem">
                        <nav style="border-inline-start:2px solid var(--border,#e5e7eb);padding-inline-start:1rem">
                            <strong style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted,#6b7280);display:block;margin-bottom:0.5rem">{{ __('api-docs.on_this_page') }}</strong>
                            <a href="#introduction" style="display:block;padding:0.35rem 0;font-size:0.875rem;color:var(--text-muted,#6b7280);text-decoration:none">{{ __('api-docs.introduction_section') }}</a>
                            <a href="#authentication" style="display:block;padding:0.35rem 0;font-size:0.875rem;color:var(--text-muted,#6b7280);text-decoration:none">{{ __('api-docs.authentication_section') }}</a>
                            <a href="#endpoints" style="display:block;padding:0.35rem 0;font-size:0.875rem;color:var(--text-muted,#6b7280);text-decoration:none">{{ __('api-docs.endpoints_section') }}</a>
                            <a href="#errors" style="display:block;padding:0.35rem 0;font-size:0.875rem;color:var(--text-muted,#6b7280);text-decoration:none">{{ __('api-docs.errors_section') }}</a>
                            <a href="#rate-limits" style="display:block;padding:0.35rem 0;font-size:0.875rem;color:var(--text-muted,#6b7280);text-decoration:none">{{ __('api-docs.rate_limits_section') }}</a>
                            <a href="#sdk" style="display:block;padding:0.35rem 0;font-size:0.875rem;color:var(--text-muted,#6b7280);text-decoration:none">{{ __('api-docs.sdk_section') }}</a>
                        </nav>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="api-docs-content">

                        <h2 id="introduction">{{ __('api-docs.introduction_section') }}</h2>
                        <p>{{ __('api-docs.introduction') }}</p>
                        <p>{{ __('api-docs.base_url') }}</p>
                        <div class="code-block-wrapper" dir="ltr">
                            <pre><code>{{ $baseUrl }}</code></pre>
                        </div>

                        <h3>{{ __('api-docs.quick_start') }}</h3>
                        <p>Follow these steps to start using the API in minutes:</p>
                        <ol style="color:var(--text-muted,#6b7280);line-height:2">
                            <li><strong>Get your token:</strong> <code>POST {{ $baseUrl }}/auth/login</code> with email and password</li>
                            <li><strong>Use the token:</strong> Include <code>Authorization: Bearer YOUR_TOKEN</code> in all requests</li>
                            <li><strong>Pick a workspace:</strong> List workspaces via <code>GET {{ $baseUrl }}/workspaces</code> and get the ID</li>
                            <li><strong>Manage data:</strong> Use workspace endpoints like <code>{{ $baseUrl }}/workspace/incomes</code></li>
                        </ol>

                        <h2 id="authentication">{{ __('api-docs.authentication_section') }}</h2>
                        <p>{{ __('api-docs.authentication_description') }}</p>
                        <div class="info-box"><p><strong>{{ __('api-docs.authentication_note') }}</strong></p></div>
                        <p>{{ __('api-docs.how_to_get_token') }}</p>
                        <p>{{ __('api-docs.how_to_get_token2') }}</p>
                        <p>{{ __('api-docs.how_to_get_token3') }}</p>

                        <h3>{{ __('api-docs.common_operations') }}</h3>
                        <p>Full code examples for the most frequently used API operations.</p>

                        <h4>{{ __('api-docs.list_incomes_example') }}</h4>
                        @include('api-docs._code_block', ['id'=>'list-incomes','examples'=>[
                            'curl' => 'curl "' . $baseUrl . '/workspace/incomes?page=1&per_page=15" \\\n  -H "Authorization: Bearer YOUR_TOKEN"',
                            'php' => '$ch = curl_init(\'' . $baseUrl . '/workspace/incomes?page=1&per_page=15\');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    \'Authorization: Bearer YOUR_TOKEN\',
    \'Accept: application/json\',
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$data = json_decode($response, true);',
                            'python' => 'import requests
headers = {"Authorization": "Bearer YOUR_TOKEN"}
r = requests.get("' . $baseUrl . '/workspace/incomes", params={"page":1,"per_page":15}, headers=headers)
data = r.json()',
                            'javascript' => 'const response = await fetch("' . $baseUrl . '/workspace/incomes?page=1&per_page=15", {
  headers: { Authorization: "Bearer YOUR_TOKEN" }
});
const data = await response.json();',
                        ],'response'=>json_encode(['data'=>[['id'=>1,'amount'=>1000,'description'=>'Freelance','category'=>'Freelance','date'=>'2025-06-01']],'meta'=>['current_page'=>1,'per_page'=>15,'total'=>1,'last_page'=>1]], JSON_PRETTY_PRINT)])

                        <h4>{{ __('api-docs.create_income_example') }}</h4>
                        @include('api-docs._code_block', ['id'=>'create-income','examples'=>[
                            'curl' => 'curl -X POST "' . $baseUrl . '/workspace/incomes" \\\n  -H "Authorization: Bearer YOUR_TOKEN" \\\n  -H "Content-Type: application/json" \\\n  -d \'{"amount":1500,"description":"Freelance project","category_id":1,"date":"2025-06-15"}\'',
                            'php' => '$ch = curl_init(\'' . $baseUrl . '/workspace/incomes\');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    \'amount\' => 1500,
    \'description\' => \'Freelance project\',
    \'category_id\' => 1,
    \'date\' => \'2025-06-15\',
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    \'Authorization: Bearer YOUR_TOKEN\',
    \'Content-Type: application/json\',
    \'Accept: application/json\',
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);',
                            'python' => 'import requests
headers = {"Authorization": "Bearer YOUR_TOKEN", "Content-Type": "application/json"}
data = {"amount":1500,"description":"Freelance project","category_id":1,"date":"2025-06-15"}
r = requests.post("' . $baseUrl . '/workspace/incomes", json=data, headers=headers)
result = r.json()',
                            'javascript' => 'const response = await fetch("' . $baseUrl . '/workspace/incomes", {
  method: "POST",
  headers: {
    Authorization: "Bearer YOUR_TOKEN",
    "Content-Type": "application/json"
  },
  body: JSON.stringify({
    amount: 1500,
    description: "Freelance project",
    category_id: 1,
    date: "2025-06-15"
  })
});
const result = await response.json();',
                        ],'response'=>json_encode(['id'=>1,'amount'=>1500,'description'=>'Freelance project','category_id'=>1,'date'=>'2025-06-15','message'=>'Income created successfully'], JSON_PRETTY_PRINT)])

                        @include('api-docs._code_block', ['id'=>'auth','examples'=>[
                            'curl' => 'curl -X POST "' . $baseUrl . '/auth/login" \\\n  -H "Content-Type: application/json" \\\n  -d \'{"email":"user@example.com","password":"your-password"}\'',
                            'php' => '$ch = curl_init(\'' . $baseUrl . '/auth/login\');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([\'email\'=>\'user@example.com\',\'password\'=>\'your-password\']));
curl_setopt($ch, CURLOPT_HTTPHEADER, [\'Content-Type: application/json\',\'Accept: application/json\']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);',
                            'python' => 'import requests
r = requests.post("' . $baseUrl . '/auth/login", json={"email":"user@example.com","password":"your-password"})',
                            'javascript' => 'const r = await fetch("' . $baseUrl . '/auth/login", {
  method:"POST", headers:{"Content-Type":"application/json"},
  body: JSON.stringify({email:"user@example.com",password:"your-password"})
})',
                            'ruby' => 'require "net/http"
uri = URI("' . $baseUrl . '/auth/login")
req = Net::HTTP::Post.new(uri)
req["Content-Type"] = "application/json"
req.body = {email:"user@example.com",password:"your-password"}.to_json
res = Net::HTTP.start(uri.hostname, uri.port, use_ssl:true) {|h| h.request(req)}',
                        ],'response'=>json_encode(['user'=>['id'=>1,'name'=>'John Doe','email'=>'user@example.com'],'token'=>'1|abc123...','message'=>'Login successful'], JSON_PRETTY_PRINT)])

                        <h2 id="endpoints">{{ __('api-docs.endpoints_section') }}</h2>
                        <p>{{ __('api-docs.endpoints_description') }}</p>
                        <p class="small text-muted">{{ __('api-docs.with_abilities') }} <span class="ability-tag">*</span></p>

                        @foreach($grouped as $groupKey => $endpoints)
                            <h3 class="mt-4"><i class="{{ $groupIcons[$groupKey] ?? 'bi-chevron-right' }} me-1"></i> {{ __("api-docs.{$groupKey}") }}</h3>
                            @foreach($endpoints as $ep)
                                <x-api-endpoint method="{{ $ep['method'] }}" endpoint="{{ $ep['endpoint'] }}" desc="{{ __($ep['desc_key']) }}" ability="{{ $ep['ability'] }}">
                                    @if(isset($ep['params']))
                                        <h4>{{ __('api-docs.parameters') }}</h4>
                                        <div class="table-responsive"><table class="param-table">
                                            <thead><tr><th>{{ __('api-docs.parameters') }}</th><th>Type</th><th>{{ __('api-docs.description') }}</th></tr></thead>
                                            <tbody>
                                                @foreach($ep['params'] as $p)
                                                    <tr><td>{{ $p[0] }} {!! $p[3] ? '<span class="param-required">*</span>' : '' !!}</td><td class="param-type">{{ $p[1] }}</td><td>{{ $p[2] }}</td></tr>
                                                @endforeach
                                            </tbody>
                                        </table></div>
                                    @endif
                                    @php
                                        $m = strtolower($ep['method']);
                                        $url = $ep['endpoint'];
                                        $curlEx = "curl -X {$ep['method']} \"{$baseUrl}{$url}\" -H \"Authorization: Bearer YOUR_TOKEN\"";
                                        if (in_array($m, ['post','put','patch']) && isset($ep['params'])) {
                                            $sample = '{}';
                                            $curlEx .= " -H \"Content-Type: application/json\" -d '{$sample}'";
                                        }
                                    @endphp
                                    @include('api-docs._code_block', ['examples'=>['curl'=>$curlEx],'id'=>'ep-'.Str::slug($url)])
                                </x-api-endpoint>
                            @endforeach
                        @endforeach

                        <h2 id="errors">{{ __('api-docs.error_codes_title') }}</h2>
                        <div class="table-responsive"><table class="param-table" style="margin-bottom:1.5rem">
                            <thead><tr><th>Code</th><th>{{ __('api-docs.description') }}</th></tr></thead>
                            <tbody>
                                <tr><td><span class="badge bg-danger">401</span></td><td>{{ __('api-docs.error_401') }}</td></tr>
                                <tr><td><span class="badge bg-warning text-dark">403</span></td><td>{{ __('api-docs.error_403') }}</td></tr>
                                <tr><td><span class="badge bg-secondary">404</span></td><td>{{ __('api-docs.error_404') }}</td></tr>
                                <tr><td><span class="badge bg-info text-dark">422</span></td><td>{{ __('api-docs.error_422') }}</td></tr>
                                <tr><td><span class="badge bg-warning text-dark">429</span></td><td>{{ __('api-docs.error_429') }}</td></tr>
                                <tr><td><span class="badge bg-danger">500</span></td><td>{{ __('api-docs.error_500') }}</td></tr>
                            </tbody>
                        </table></div>

                        <h2 id="rate-limits">{{ __('api-docs.rate_limits_title') }}</h2>
                        <p>{{ __('api-docs.rate_limits_desc') }}</p>
                        <ul>
                            <li>{{ __('api-docs.rate_general') }}</li>
                            <li>{{ __('api-docs.rate_workspace') }}</li>
                        </ul>
                        <p>{{ __('api-docs.rate_exceeded') }}</p>
                        <p><strong>Headers:</strong> <code>X-RateLimit-Limit</code>, <code>X-RateLimit-Remaining</code>, <code>Retry-After</code></p>

                        <h2 id="sdk">{{ __('api-docs.sdk_title') }}</h2>
                        <p>{{ __('api-docs.sdk_description') }}</p>
                        <div class="grid-3">
                            <div class="sdk-card">
                                <i class="bi bi-code-square"></i>
                                <h4>{{ __('api-docs.sdk_php') }}</h4>
                                <p>{{ __('api-docs.sdk_php_desc') }}</p>
                                <code style="font-size:0.8rem">composer require finance-manager/client</code>
                            </div>
                            <div class="sdk-card">
                                <i class="bi bi-braces"></i>
                                <h4>{{ __('api-docs.sdk_js') }}</h4>
                                <p>{{ __('api-docs.sdk_js_desc') }}</p>
                                <code style="font-size:0.8rem">npm install finance-manager-client</code>
                            </div>
                            <div class="sdk-card">
                                <i class="bi bi-terminal"></i>
                                <h4>{{ __('api-docs.sdk_python') }}</h4>
                                <p>{{ __('api-docs.sdk_python_desc') }}</p>
                                <code style="font-size:0.8rem">pip install finance-manager</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function initApiDocs() {
        document.querySelectorAll('.endpoint-card-header').forEach(function(h) {
            h.addEventListener('click', function() { this.parentElement.classList.toggle('expanded'); });
        });
        document.querySelectorAll('.code-block').forEach(function(block) {
            var tabs = block.querySelectorAll('.code-tab');
            var pre = block.querySelectorAll('.code-pre');
            tabs.forEach(function(tab, i) {
                tab.addEventListener('click', function() {
                    tabs.forEach(function(t) { t.classList.remove('active'); });
                    pre.forEach(function(p) { p.style.display = 'none'; });
                    tab.classList.add('active');
                    if (pre[i]) pre[i].style.display = 'block';
                });
            });
        });
        document.querySelectorAll('.copy-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var code = btn.closest('.code-block-wrapper').querySelector('pre code');
                var text = code.textContent;
                var orig = btn.textContent;
                var done = function() { btn.textContent = '{{ __("api-docs.code_copied") }}'; setTimeout(function() { btn.textContent = orig; }, 2000); };
                if (navigator.clipboard) { navigator.clipboard.writeText(text).then(done).catch(function() { var ta = document.createElement('textarea'); ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0'; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); done(); }); }
                else { var ta = document.createElement('textarea'); ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0'; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); done(); }
            });
        });
    }
    initApiDocs();
    </script>
    @endpush
</x-guest-layout>
