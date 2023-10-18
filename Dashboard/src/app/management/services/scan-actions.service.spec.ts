import { TestBed } from '@angular/core/testing';

import { ScanActionsService } from './scan-actions.service';

describe('ScanActionsService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: ScanActionsService = TestBed.get(ScanActionsService);
    expect(service).toBeTruthy();
  });
});
