import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ItemsLostDetailNowComponent } from './items-lost-detail-now.component';

describe('ItemsLostDetailNowComponent', () => {
  let component: ItemsLostDetailNowComponent;
  let fixture: ComponentFixture<ItemsLostDetailNowComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ItemsLostDetailNowComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ItemsLostDetailNowComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
