$authConpany = Auth::guard('company')->user()->id;
        $companyId = searchCompanyId($authConpany);
        if ($request->isMethod('post')) {
            dd($request->all());
            $request->validate([
                'project_name' => 'required',
                'planned_start_date' => 'required',
                'address' => 'required',
                'planned_end_date' => 'date',
                'tag_company' => 'required',
                'own_project_or_contractor' => 'required|in:yes,no',
                'client_name' => 'required_if:own_project_or_contractor,yes',
                'client_company_address' => 'required_if:own_project_or_contractor,yes',
                'client_company_name' => 'required_if:own_project_or_contractor,yes',
                'client_designation' => 'required_if:own_project_or_contractor,yes',
                'client_email' => 'required_if:own_project_or_contractor,yes',
                'client_phone' => 'required_if:own_project_or_contractor,yes',
            ]);
            // dd($request->all());
            DB::beginTransaction();
            if ($request->uuid) {
                try {
                    $pid = uuidtoid($request->uuid, 'projects');
                    $fetchLogo = Project::find($pid);
                    $isProjectUpdated = Project::where('id', $pid)->update([
                        'project_name' => $request->project_name,
                        'planned_start_date' => $request->planned_start_date,
                        'address' => $request->address,
                        'planned_end_date' => $request->planned_end_date,
                        'own_project_or_contractor' => $request->own_project_or_contractor,
                        'project_completed' => $request->project_completed == 'yes' ? 'yes' : 'no',
                        'company_id' => $companyId,
                        'companies_id' => $request->tag_company,
                        'project_completed_date' => $request->project_completed_date,
                        'logo' => $request->logo ? getImgUpload($request->logo, 'logo') : $fetchLogo->logo,
                    ]);
                    // }
                    // dd($request->own_project_or_contractor);
                    if ($request->own_project_or_contractor == 'yes') {
                        if ($request->clientUuid != null) {
                            $cid = uuidtoid($request->clientUuid, 'clients');
                            $isClientUpdated = Client::where('id', $cid)->where('project_id', $pid)->update([
                                'client_name' => $request->client_name,
                                'client_designation' => $request->client_designation,
                                'client_email' => $request->client_email,
                                'client_phone' => $request->client_phone,
                                'client_mobile' => $request->client_mobile,
                                'client_company_name' => $request->client_company_name,
                                'client_company_address' => $request->client_company_address,
                            ]);
                            if ($isClientUpdated) {
                                DB::commit();
                                return redirect()->route('company.project.list')->with('success', 'Project Updated Successfully');
                            }
                        } else {
                            // dd($isProjectUpdated);
                            $isClientCreated = Client::create([
                                'uuid' => Str::uuid(),
                                'client_name' => $request->client_name,
                                'client_designation' => $request->client_designation,
                                'client_email' => $request->client_email,
                                'client_phone' => $request->client_phone,
                                'client_mobile' => $request->client_mobile,
                                'client_company_name' => $request->client_company_name,
                                'client_company_address' => $request->client_company_address,
                                'project_id' => $pid,
                            ]);
                            if ($isClientCreated) {
                                // dd($request->all());
                                DB::commit();
                                return redirect()->route('company.project.list')->with('success', 'Project Updated Successfully');
                            }
                        }
                    } else {
                        // dd($request->all());
                        if ($request->clientUuid) {
                            $cid = uuidtoid($request->clientUuid, 'clients');
                            $res = Client::where('id', $cid)->delete();
                        }
                        DB::commit();
                        return redirect()->route('company.project.list')->with('success', 'Project Updated Successfully');
                    }
                    //  dd($request->all());
                } catch (\Exception $e) {
                    DB::rollBack();
                    logger($e->getMessage() . '--' . $e->getFile() . '--' . $e->getLine());
                    return redirect()->route('company.project.list')->with('error', 'something want to be worng');
                }
            } else {
                //create a new project
                try {
                    $isProjectCreated = Project::create([
                        'uuid' => Str::uuid(),
                        'project_name' => $request->project_name,
                        'planned_start_date' => $request->planned_start_date,
                        'address' => $request->address,
                        'planned_end_date' => $request->planned_end_date,
                        'own_project_or_contractor' => $request->own_project_or_contractor,
                        'project_completed' => $request->project_completed == 'yes' ? 'yes' : 'no',
                        'company_id' => $companyId,
                        'companies_id' => $request->tag_company,
                        'project_completed_date' => $request->project_completed_date,
                        'logo' => $request->logo ? getImgUpload($request->logo, 'logo') : '',
                    ]);
                    if ($request->own_project_or_contractor == 'yes') {
                        $isClientCreated = Client::create([
                            'uuid' => Str::uuid(),
                            'client_name' => $request->client_name,
                            'client_designation' => $request->client_designation,
                            'client_email' => $request->client_email,
                            'client_phone' => $request->client_phone,
                            'client_mobile' => $request->client_mobile,
                            'client_company_name' => $request->client_company_name,
                            'client_company_address' => $request->client_company_address,
                            'project_id' => $isProjectCreated->id,
                        ]);
                    }
                    $isSubProjectCreated = StoreWarehouse::create([
                        'uuid' => Str::uuid(),
                        'name' => 'Main Store',
                        'location' => Null,
                        'projects_id' => $isProjectCreated->id,
                        'company_id' => $companyId,
                    ]);

                    if ($isProjectCreated) {
                        DB::commit();
                        return redirect()->route('company.project.list')->with('success', 'Project Created Successfully');
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    // dd($e->getMessage());
                    logger($e->getMessage() . '--' . $e->getFile() . '--' . $e->getLine());
                    return redirect()->route('company.project.list')->with('error', $e->getMessage());
                }
            }
        }
        return view('Company.projects.add-edit');
