import { TestBed } from '@angular/core/testing';

import { TargetKpiService } from './target-kpi.service';

describe('TargetKpiService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: TargetKpiService = TestBed.get(TargetKpiService);
    expect(service).toBeTruthy();
  });
});
